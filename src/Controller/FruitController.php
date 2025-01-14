<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use App\Entity\Fruit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Fruits",
 *     description="Endpoints related to fruits"
 * )
 */
class FruitController extends BaseApiController
{
    #[Route('/api/fruits', methods: ['GET'])]
    /**
     *
     * @OA\Get(
     *     path="/api/fruits",
     *     summary="List all fruits",
     *     @OA\Response(
     *         response=200,
     *         description="Returns fruits list",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Fruit"))
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by fruit's name (partial search also)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="grams",
     *         in="query",
     *         description="Filter by weight in grams",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Units (grams or kilograms)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"grams", "kilograms"},
     *             default="grams"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="min_grams",
     *         in="query",
     *         description="Filter by weight min in grams",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="max_grams",
     *         in="query",
     *         description="Filter by weight max in grams",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     )
     * )
     */
    public function getFruits(Request $request): JsonResponse
    {
        return $this->getDataWithFilters('fruits', $request);
    }

    #[Route('/api/fruits/{id}', name: 'get_fruit', methods: ['GET'])]
    /**
     *
     * @OA\Get(
     *     path="/api/fruits/{id}",
     *     summary="Retrieve a fruit by its ID",
     *     tags={"Fruits"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the fruit",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Units (grams or kilograms)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"grams", "kilograms"},
     *             default="grams"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a fruit",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="grams", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Fruit not found"
     *     )
     * )
     */
    public function get(string $id, Request $request): JsonResponse
    {
        return $this->getById('fruits', $id, $request);
    }

    #[Route('/api/fruits', methods: ['POST'])]
    /**
     *
     * @OA\Post(
     *     path="/api/fruits",
     *     summary="Add a fruit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Fruit")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Fruit added successfully"
     *     )
     * )
     */
    public function addFruit(Request $request): JsonResponse
    {
        $fruit = $this->deserializeAndValidate($request, Fruit::class);
        if ($fruit instanceof JsonResponse) {
            return $fruit; // Return validation errors if present
        }

        $data = json_decode($request->getContent(), true);
        $this->storageService->saveData($data, 'fruits');

        return new JsonResponse(['status' => 'Fruit added successfully'], 201);
    }
}
