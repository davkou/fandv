<?php
namespace App\Controller\Base;

use App\Service\StorageService;
use App\Service\UnitConversionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseApiController extends AbstractController
{
    public function __construct(
        protected StorageService $storageService,
        protected UnitConversionService $unitConversionService,
        protected ValidatorInterface $validator,
        protected SerializerInterface $serializer,
    ) {
    }

    protected function deserializeAndValidate(Request $request, string $class)
    {
        try {
            // Deserialize the request into an object of the specified class
            $object = $this->serializer->deserialize($request->getContent(), $class, 'json');

            // Validate the object
            $violations = $this->validator->validate($object);

            // If there are violations, return an error response
            if (count($violations) > 0) {
                return new JsonResponse([
                    'errors' => (string) $violations,
                ], 422);  // Return a 422 error in case of validation issues
            }

            return $object;
        } catch (NotNormalizableValueException $e) {
            return new JsonResponse(['error' => sprintf('Invalid data format %s', $e->getMessage())], 422);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }

    protected function getDataWithFilters(string $entity, Request $request, array $filters = []): JsonResponse
    {
        // Get the filters from the request
        $filters = array_merge($filters, $request->query->all());
        $unit = $filters['unit'] ?? 'grams';

        // Handle weight filters (min/max)
        $minGrams = $filters['min_grams'] ?? null;
        $maxGrams = $filters['max_grams'] ?? null;
        unset($filters['min_grams'], $filters['max_grams'], $filters['unit']);

        // Apply the filters and retrieve the data from the repository
        $data = $this->storageService->getAllData($entity, $filters);

        // Apply weight filters if provided
        if ($minGrams !== null) {
            $data = array_filter($data, fn ($item) => $item['grams'] >= $minGrams);
        }
        if ($maxGrams !== null) {
            $data = array_filter($data, fn ($item) => $item['grams'] <= $maxGrams);
        }

        // Apply the unit conversion
        $data = $this->unitConversionService->applyUnitConversion($data, $unit);

        return new JsonResponse($data);
    }

    protected function getById(string $entity, string $id, Request $request): JsonResponse
    {
        // Get the desired unit from the request parameters
        $unit = $request->query->get('unit', 'grams');

        // Search for the specific item
        $item = $this->storageService->findData($id, $entity);
        if (!$item) {
            return new JsonResponse(['error' => 'Not found'], 404);
        }

        // Apply the unit conversion
        $item = $this->unitConversionService->applyUnitConversion($item, $unit);

        return new JsonResponse($item);
    }
}
