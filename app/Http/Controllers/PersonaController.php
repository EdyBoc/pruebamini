<?php
namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PersonaController extends Controller
{
    public function index()
    {
        return Persona::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'tipo_documento' => 'required|string',
            'cui' => 'required|string|max:13|unique:personas,cui',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|max:8',
            'email' => 'required|email|max:255',
            'sexo' => 'required|string|in:M,F,Otro',
            'estado_civil' => 'required|string|max:100',
            'direccion' => 'required|string',
            'archivo' => 'nullable|file|mimes:pdf|max:1024', // 1MB
        ]);

        if ($request->hasFile('archivo')) {
            $path                      = $request->file('archivo')->store('archivos', 'public');
            $validated['ruta_archivo'] = $path;
        }

        $validated['estado'] = 'pendiente';
        Persona::create($validated);

        return response()->json(['message' => 'Persona registrada correctamente']);
    }

    public function show(Persona $persona)
    {
        return $persona;
        //http://127.0.0.1:8000/personas/crear
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $validated = $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'tipo_documento' => 'required|string',
            'cui' => 'required|string|max:13|unique:personas,cui',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|max:8',
            'email' => 'required|email|max:255',
            'sexo' => 'required|string|in:M,F,Otro',
            'estado_civil' => 'required|string|max:100',
            'direccion' => 'required|string',
            'archivo' => 'nullable|file|mimes:pdf|max:1024',
        ]);

        if ($request->has('estado')) {
            $persona->estado = $validated['estado'];
            $persona->save();
            return response()->json(['message' => 'Estado actualizado']);
        }

        if ($request->hasFile('archivo')) {
            $path                      = $request->file('archivo')->store('archivos', 'public');
            $validated['ruta_archivo'] = $path;
        }

        $persona->update($validated);

        return response()->json(['message' => 'Persona actualizada']);
    }

    public function destroy(Persona $persona)
    {
        if ($persona->ruta_archivo) {
            Storage::disk('public')->delete($persona->ruta_archivo);
        }

        $persona->delete();
        return response()->noContent();
    }
}
