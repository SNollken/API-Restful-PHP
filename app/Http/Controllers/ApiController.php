<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use App\Repositories\StudentRepository;
use App\Http\Resources\StudentResource;

class ApiController extends Controller
{
    // Lista todos os estudantes
    public function index()
    {
        $service = new StudentService(new StudentRepository());
        $students = $service->getAllStudents();
        return StudentResource::collection($students);
    }

    // Cria um novo estudante
    public function store(StoreStudentRequest $request)
    {
        $service = new StudentService(new StudentRepository());
        $student = $service->createStudent($request->validated());

        return response()->json([
            "message" => "Estudante criado com sucesso!",
            "data" => new StudentResource($student)
        ], 201);
    }

    // Retorna um estudante específico
    public function show($id)
    {
        $service = new StudentService(new StudentRepository());
        $student = $service->getStudent($id);

        if ($student) {
            return new StudentResource($student);
        } else {
            return response()->json(["message" => "Estudante não encontrado"], 404);
        }
    }

    // Atualiza um estudante
    public function update(UpdateStudentRequest $request, $id)
    {
        $service = new StudentService(new StudentRepository());
        $student = $service->updateStudent($id, $request->validated());

        if ($student) {
            return response()->json([
                "message" => "Estudante atualizado com sucesso!",
                "data" => new StudentResource($student)
            ], 200);
        } else {
            return response()->json(["message" => "Estudante não encontrado"], 404);
        }
    }

    // Deleta um estudante
    public function destroy($id)
    {
        $service = new StudentService(new StudentRepository());
        $deleted = $service->deleteStudent($id);

        if ($deleted) {
            return response()->json(["message" => "Estudante deletado com sucesso!"], 202);
        } else {
            return response()->json(["message" => "Estudante não encontrado"], 404);
        }
    }
}
