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
 *     name="Vegetables",
 *     description="Endpoints related to vegetables"
 * )
 */
class VegetableController extends AbstractController
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * @Route("/api/vegetables", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/vegetables",
     *     summary="Liste tous les légumes",
     *     @OA\Response(
     *         response=200,
     *         description="Retourne la liste des légumes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Vegetable"))
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtrer par nom de légume",
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
    public function getVegetables(Request $request): JsonResponse
    {
        // Récupérer les filtres depuis la requête
        $filters = $request->query->all();

        // Appliquer les filtres et récupérer les légumes
        $vegetables = $this->storageService->getAllData('vegetables', $filters);

        return new JsonResponse($vegetables);
    }

    /**
     * @Route("/api/vegetables/{id}", name="get_vegetables", methods={"GET"})
     *
     * @OA\Get(
     *     path="/vegetables/{id}",
     *     summary="Retrieve a fruit by its ID",
     *     tags={"Vegetables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the vegetable",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a vegetable",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="color", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vegetable not found"
     *     )
     * )
     */
    public function get(string $id): JsonResponse
    {
        $fruit = $this->storageService->findData($id, 'vegetables');
        return $fruit ? new JsonResponse($fruit) : new JsonResponse(['error' => 'Vegetable not found'], 404);
    }

    /**
     * @Route("/api/vegetables", methods={"POST"})
     *
     * @OA\Post(
     *     path="/api/vegetables",
     *     summary="Ajoute un légume",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Vegetable")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Légume ajouté avec succès"
     *     )
     * )
     */
    public function addVegetable(Request $request): JsonResponse
    {
        // Récupérer les données envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        // Ajouter un légume
        $this->storageService->saveData($data, 'vegetables');

        return new JsonResponse(['status' => 'Vegetable added successfully'], 201);
    }
}
