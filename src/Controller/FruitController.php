<?php

namespace App\Controller;

use App\Service\StorageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Fruits",
 *     description="Endpoints related to fruits"
 * )
 */
class FruitController extends AbstractController
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * @Route("/api/fruits", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/fruits",
     *     summary="Liste tous les fruits",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne la liste des fruits",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Fruit"))
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtrer par nom de fruit",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="grams",
     *         in="query",
     *         description="Filtrer par poids en grammes",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     )
     * )
     */
    public function getFruits(Request $request): JsonResponse
    {
        // Récupérer les filtres depuis la requête
        $filters = $request->query->all();

        // Appliquer les filtres et récupérer les fruits depuis le repository "fruits"
        $fruits = $this->storageService->getAllData('fruits', $filters);

        return new JsonResponse($fruits);
    }

    /**
     * @Route("/api/fruits/{id}", name="get_fruit", methods={"GET"})
     *
     * @OA\Get(
     *     path="/fruits/{id}",
     *     summary="Retrieve a fruit by its ID",
     *     tags={"Fruits"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the fruit",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a fruit",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="color", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Fruit not found"
     *     )
     * )
     */
    public function get(string $id): JsonResponse
    {
        // Recherche d'un fruit spécifique dans le repository "fruits"
        $fruit = $this->storageService->findData($id, 'fruits');
        return $fruit ? new JsonResponse($fruit) : new JsonResponse(['error' => 'Fruit not found'], 404);
    }

    /**
     * @Route("/api/fruits", methods={"POST"})
     *
     * @OA\Post(
     *     path="/api/fruits",
     *     summary="Ajoute un fruit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Fruit")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Fruit ajouté avec succès"
     *     )
     * )
     */
    public function addFruit(Request $request): JsonResponse
    {
        // Récupérer les données envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        // Ajouter un fruit dans le repository "fruits"
        $this->storageService->saveData($data, 'fruits');

        return new JsonResponse(['status' => 'Fruit added successfully'], 201);
    }
}
