<?php
declare(strict_types=1);

namespace Progi\Infrastructure\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Application\DTO\CalculateBidRequest;
use Progi\Domain\Model\VehicleType;
use Progi\Domain\Exception\InvalidPriceException;
use Progi\Domain\Exception\PolicyNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Controller for calculating bid fees.
 *
 * Routes in this controller are automatically prefixed with /api/v1 via routes.yaml.
 */
#[Route('/bid/calculate', name: 'api_bid_calculate', methods: ['POST'])]
class CalculateBidFeeController extends AbstractController
{
    /**
     * @param LoggerInterface $logger Logger instance injected via DI.
     * @param ValidatorInterface $validator Validator instance injected via DI.
     */
    public function __construct(
        private LoggerInterface $logger,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Handles POST requests to calculate bid fees.
     *
     * @param Request $request
     * @param CalculateBidUseCase $useCase
     * @return JsonResponse
     */
    public function __invoke(Request $request, CalculateBidUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $vehicleType = VehicleType::tryFrom($data['type'] ?? '') ?? VehicleType::Common;

        $input = new CalculateBidRequest(
            price: (float)($data['price'] ?? 0),
            vehicleType: $vehicleType->value
        );

        $violations = $this->validator->validate($input);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], 400);
        }

        try {
            $dto = $useCase->execute($input->price, $input->vehicleType);
            $this->logger->debug("Calculated fees for vehicleType={$input->vehicleType}, price={$input->price}");
            return $this->json($dto->toArray());
        } catch (InvalidPriceException | PolicyNotFoundException $e) {
            $this->logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
