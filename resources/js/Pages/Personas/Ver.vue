<script setup>
import { usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

// Obtener usuario y rol
const user = usePage().props.auth.user;
const rol = user.roles?.[0] || null;
console.log("Rol del usuario autenticado:", rol);

const personas = ref([]);

const obtenerPersonas = async () => {
    const res = await axios.get("/api/personas");
    personas.value = res.data;
};

onMounted(() => {
    obtenerPersonas();
});

const modoEdicion = ref(false);
const personaEditando = ref(null);

const form = ref({
    primer_nombre: "",
    segundo_nombre: "",
    primer_apellido: "",
    segundo_apellido: "",
    apellido_casada: "",
    tipo_documento: "",
    pasaporte: "",
    cui: "",
    fecha_nacimiento: "",
    telefono: "",
    email: "",
    sexo: "",
    estado_civil: "",
    direccion: "",
});

const archivo = ref(null);

const handleArchivo = (e) => {
    archivo.value = e.target.files[0];
};

const editarPersona = (persona) => {
    modoEdicion.value = true;
    personaEditando.value = persona.id;
    form.value = { ...persona };
};

const cancelarEdicion = () => {
    modoEdicion.value = false;
    personaEditando.value = null;
    form.value = {
        primer_nombre: "",
        segundo_nombre: "",
        primer_apellido: "",
        segundo_apellido: "",
        apellido_casada: "",
        tipo_documento: "",
        pasaporte: "",
        cui: "",
        fecha_nacimiento: "",
        telefono: "",
        email: "",
        sexo: "",
        estado_civil: "",
        direccion: "",
    };
    archivo.value = null;
};

const errores = ref({});

const guardarPersona = async () => {
    errores.value = {};
    try {
        const data = new FormData();
        Object.keys(form.value).forEach((key) => {
            data.append(key, form.value[key] ?? "");
        });
        if (archivo.value) {
            data.append("archivo", archivo.value);
        }

        if (modoEdicion.value) {
            await axios.post(
                `/api/personas/${personaEditando.value}?_method=PUT`,
                data
            );
            alert("Persona actualizada");
        } else {
            await axios.post("/api/personas", data);
            alert("Persona registrada");
        }

        await obtenerPersonas();
        cancelarEdicion();
    } catch (error) {
        if (error.response?.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            console.error(error);
            alert("Error inesperado");
        }
    }
};

const eliminarPersona = async (id) => {
    if (!confirm("¿Estás seguro de eliminar esta persona?")) return;
    try {
        await axios.delete(`/api/personas/${id}`);
        await obtenerPersonas();
        alert("Persona eliminada");
    } catch (error) {
        console.error(error);
        alert("Error al eliminar persona");
    }
};

const verArchivo = (ruta) => {
    const url = `${import.meta.env.VITE_APP_URL}/storage/${ruta}`;
    window.open(url, "_blank");
};

const cambiarEstado = async (id, nuevoEstado) => {
    try {
        await axios.put(`/api/personas/${id}`, { estado: nuevoEstado });
        await obtenerPersonas();
        alert(`Estado actualizado a ${nuevoEstado}`);
    } catch (error) {
        console.error(error);
        alert("Error al cambiar el estado");
    }
};
</script>

<template>
    <div class="p-6 max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-blue-800 mb-8">Lista de Personas Registradas</h1>

        <table class="w-full text-sm text-left text-gray-700 bg-white shadow-lg rounded-xl overflow-hidden">
            <thead class="text-xs uppercase bg-gradient-to-r from-blue-700 to-blue-500 text-white">
                <tr>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Apellido</th>
                    <th class="px-6 py-3">CUI</th>
                    <th class="px-6 py-3">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in personas" :key="p.id" class="border-b hover:bg-blue-50 transition duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                        {{ p.primer_nombre }} {{ p.segundo_nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ p.primer_apellido }} {{ p.segundo_apellido }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ p.cui }}</td>
                    <td class="px-6 py-4 font-semibold capitalize whitespace-nowrap" :class="{
                        'text-yellow-500': p.estado === 'pendiente',
                        'text-green-600': p.estado === 'aprobado',
                        'text-red-600': p.estado === 'rechazado'
                    }">
                        {{ p.estado }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <template v-if="rol === 'Técnico Analista'">
                            <div class="flex flex-wrap justify-center gap-2">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm" @click="editarPersona(p)">
                                    Editar
                                </button>
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm" @click="eliminarPersona(p.id)">
                                    Eliminar
                                </button>
                                <button v-if="p.ruta_archivo" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm" @click="verArchivo(p.ruta_archivo)">
                                    Ver PDF
                                </button>
                                <button v-if="p.estado === 'pendiente'" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm" @click="cambiarEstado(p.id, 'aprobado')">
                                    Aprobar
                                </button>
                                <button v-if="p.estado === 'pendiente'" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm" @click="cambiarEstado(p.id, 'rechazado')">
                                    Rechazar
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <span class="text-gray-500 italic text-sm">Sin acciones</span>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="rol === 'Técnico Analista' && modoEdicion" class="mt-10 bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-blue-800 mb-6 text-center">Editar Persona</h2>
            <form @submit.prevent="guardarPersona" class="space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Primer Nombre:</label>
                        <input v-model="form.primer_nombre" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.primer_nombre" class="text-red-600 text-sm">{{ errores.primer_nombre[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Segundo Nombre:</label>
                        <input v-model="form.segundo_nombre" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.segundo_nombre" class="text-red-600 text-sm">{{ errores.segundo_nombre[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Primer Apellido:</label>
                        <input v-model="form.primer_apellido" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.primer_apellido" class="text-red-600 text-sm">{{ errores.primer_apellido[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Segundo Apellido:</label>
                        <input v-model="form.segundo_apellido" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.segundo_apellido" class="text-red-600 text-sm">{{ errores.segundo_apellido[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Apellido de Casada:</label>
                        <input v-model="form.apellido_casada" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.apellido_casada" class="text-red-600 text-sm">{{ errores.apellido_casada[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Tipo Documento:</label>
                        <input v-model="form.tipo_documento" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.tipo_documento" class="text-red-600 text-sm">{{ errores.tipo_documento[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Pasaporte:</label>
                        <input v-model="form.pasaporte" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.pasaporte" class="text-red-600 text-sm">{{ errores.pasaporte[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">CUI:</label>
                        <input v-model="form.cui" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.cui" class="text-red-600 text-sm">{{ errores.cui[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Fecha de Nacimiento:</label>
                        <input v-model="form.fecha_nacimiento" type="date" class="border w-full p-2 rounded" />
                        <p v-if="errores.fecha_nacimiento" class="text-red-600 text-sm">{{ errores.fecha_nacimiento[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Teléfono:</label>
                        <input v-model="form.telefono" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.telefono" class="text-red-600 text-sm">{{ errores.telefono[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Email:</label>
                        <input v-model="form.email" type="email" class="border w-full p-2 rounded" />
                        <p v-if="errores.email" class="text-red-600 text-sm">{{ errores.email[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Sexo:</label>
                        <input v-model="form.sexo" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.sexo" class="text-red-600 text-sm">{{ errores.sexo[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Estado Civil:</label>
                        <input v-model="form.estado_civil" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.estado_civil" class="text-red-600 text-sm">{{ errores.estado_civil[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Dirección:</label>
                        <input v-model="form.direccion" type="text" class="border w-full p-2 rounded" />
                        <p v-if="errores.direccion" class="text-red-600 text-sm">{{ errores.direccion[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Archivo (PDF, máx 1MB):</label>
                        <input type="file" @change="handleArchivo" accept="application/pdf" class="w-full" />
                        <p v-if="errores.archivo" class="text-red-600 text-sm">{{ errores.archivo[0] }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                        Guardar
                    </button>
                    <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow" @click="cancelarEdicion">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div v-if="rol === 'Usuario Normal'" class="mt-8 text-center">
            <a href="/personas/crear" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded shadow transition-colors duration-300">
                Crear
            </a>
        </div>
    </div>
</template>
