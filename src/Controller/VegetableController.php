<?php

namespace App\Controller;

use App\Controller\Base\BaseApiController;
use App\Entity\Vegetable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Vegetables",
 *     description="Endpoints related to vegetables"
 * )
 */
class VegetableController extends BaseApiController
{
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
     *         description="Filtrer par nom de légume (partiel autorisé)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="grams",
     *         in="query",
     *         description="Filtrer par poids exact en grammes",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Unité de poids à retourner (grams ou kilograms)",
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
     *         description="Filtrer par poids minimum en grammes",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="max_grams",
     *         in="query",
     *         description="Filtrer par poids maximum en grammes",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     )
     * )
     */
    public function getVegetables(Request $request): JsonResponse
    {
        return $this->getDataWithFilters('vegetables', $request);
    }

    /**
     * @Route("/api/vegetables/{id}", name="get_vegetable", methods={"GET"})
     *
     * @OA\Get(
     *     path="/api/vegetables/{id}",
     *     summary="Retrieve a fruit by its ID",
     *     tags={"Vegetables"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the vegetable",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Unité de poids à retourner (grams ou kilograms)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"grams", "kilograms"},
     *             default="grams"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a vegetable",
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
     *         description="Vegetable not found"
     *     )
     * )
     */
    public function get(string $id, Request $request): JsonResponse
    {
        return $this->getById('vegetables', $id, $request);
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
        $vegetable = $this->deserializeAndValidate($request, Vegetable::class);
        if ($vegetable instanceof JsonResponse) {
            return $vegetable; // Return validation errors if present
        }

	    // Save the fruit in the database
        $data = json_decode($request->getContent(), true);
        $this->storageService->saveData($data, 'vegetables');

        return new JsonResponse(['status' => 'Vegetable added successfully'], 201);
    }
}
