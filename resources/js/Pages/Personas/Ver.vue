<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Obtener usuario y rol
const user = usePage().props.auth.user
const rol = user.roles?.[0] || null
console.log('Rol del usuario autenticado:', rol)

const personas = ref([])

const obtenerPersonas = async () => {
    const res = await axios.get('/api/personas')
    personas.value = res.data
}

onMounted(() => {
    obtenerPersonas()
})

const modoEdicion = ref(false)
const personaEditando = ref(null)

const form = ref({
    primer_nombre: '',
    segundo_nombre: '',
    primer_apellido: '',
    segundo_apellido: '',
    apellido_casada: '',
    tipo_documento: '',
    pasaporte: '',
    cui: '',
    fecha_nacimiento: '',
    telefono: '',
    email: '',
    sexo: '',
    estado_civil: '',
    direccion: ''
})

const archivo = ref(null)

const handleArchivo = (e) => {
    archivo.value = e.target.files[0]
}

const editarPersona = (persona) => {
    modoEdicion.value = true
    personaEditando.value = persona.id
    form.value = { ...persona }
}

const cancelarEdicion = () => {
    modoEdicion.value = false
    personaEditando.value = null
    form.value = {
        primer_nombre: '',
        segundo_nombre: '',
        primer_apellido: '',
        segundo_apellido: '',
        apellido_casada: '',
        tipo_documento: '',
        pasaporte: '',
        cui: '',
        fecha_nacimiento: '',
        telefono: '',
        email: '',
        sexo: '',
        estado_civil: '',
        direccion: ''
    }
    archivo.value = null
}

const errores = ref({})

const guardarPersona = async () => {
    errores.value = {}
    try {
        const data = new FormData()
        Object.keys(form.value).forEach(key => {
            data.append(key, form.value[key] ?? '')
        })
        if (archivo.value) {
            data.append('archivo', archivo.value)
        }

        if (modoEdicion.value) {
            await axios.post(`/api/personas/${personaEditando.value}?_method=PUT`, data)
            alert('Persona actualizada')
        } else {
            await axios.post('/api/personas', data)
            alert('Persona registrada')
        }

        await obtenerPersonas()
        cancelarEdicion()
    } catch (error) {
        if (error.response?.status === 422) {
            errores.value = error.response.data.errors
        } else {
            console.error(error)
            alert('Error inesperado')
        }
    }
}


const eliminarPersona = async (id) => {
    if (!confirm('¿Estás seguro de eliminar esta persona?')) return
    try {
        await axios.delete(`/api/personas/${id}`)
        await obtenerPersonas()
        alert('Persona eliminada')
    } catch (error) {
        console.error(error)
        alert('Error al eliminar persona')
    }
}

const verArchivo = (ruta) => {
    const url = `${import.meta.env.VITE_APP_URL}/storage/${ruta}`
    window.open(url, '_blank')
}

const cambiarEstado = async (id, nuevoEstado) => {
    try {
        await axios.put(`/api/personas/${id}`, { estado: nuevoEstado })
        await obtenerPersonas()
        alert(`Estado actualizado a ${nuevoEstado}`)
    } catch (error) {
        console.error(error)
        alert('Error al cambiar el estado')
    }
}
</script>

<template>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Listado de Personas</h1>

        <table class="table-auto w-full border mb-6">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Nombre</th>
                    <th class="border px-4 py-2">Apellido</th>
                    <th class="border px-4 py-2">CUI</th>
                    <th class="border px-4 py-2">Estado</th>
                    <th class="border px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in personas" :key="p.id">
                    <td class="border px-4 py-2">{{ p.primer_nombre }} {{ p.segundo_nombre }}</td>
                    <td class="border px-4 py-2">{{ p.primer_apellido }} {{ p.segundo_apellido }}</td>
                    <td class="border px-4 py-2">{{ p.cui }}</td>
                    <td class="border px-4 py-2 font-semibold capitalize" :class="{
                        'text-yellow-600': p.estado === 'pendiente',
                        'text-green-600': p.estado === 'aprobado',
                        'text-red-600': p.estado === 'rechazado'
                    }">
                        {{ p.estado }}
                    </td>
                    <td class="border px-4 py-2">
                        <template v-if="rol === 'Técnico Analista'">
                            <div class="flex flex-wrap gap-2">
                                <button class="bg-blue-500 text-white px-2 py-1 rounded" @click="editarPersona(p)">
                                    Editar
                                </button>
                                <button class="bg-red-600 text-white px-2 py-1 rounded" @click="eliminarPersona(p.id)">
                                    Eliminar
                                </button>
                                <button v-if="p.ruta_archivo" class="bg-indigo-600 text-white px-2 py-1 rounded"
                                    @click="verArchivo(p.ruta_archivo)">
                                    Ver PDF
                                </button>
                                <button v-if="p.estado === 'pendiente'"
                                    class="bg-green-600 text-white px-2 py-1 rounded"
                                    @click="cambiarEstado(p.id, 'aprobado')">
                                    Aprobar
                                </button>
                                <button v-if="p.estado === 'pendiente'"
                                    class="bg-yellow-600 text-white px-2 py-1 rounded"
                                    @click="cambiarEstado(p.id, 'rechazado')">
                                    Rechazar
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <span class="text-gray-500 italic text-sm">Sin acciones disponibles</span>
                        </template>
                    </td>

                </tr>
            </tbody>
        </table>

        <div v-if="rol === 'Técnico Analista' && modoEdicion" class="mb-10">
            <h2 class="text-xl font-semibold mb-4">Editar Persona</h2>
            <form @submit.prevent="guardarPersona" class="space-y-4 bg-gray-100 p-4 rounded">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Primer Nombre:</label>
                        <input v-model="form.primer_nombre" type="text" class="border w-full" />
                        <p v-if="errores.primer_nombre" class="text-red-600 text-sm">{{ errores.primer_nombre[0] }}</p>
                    </div>

                    <div>
                        <label>Segundo Nombre:</label>
                        <input v-model="form.segundo_nombre" type="text" class="border w-full" />
                        <p v-if="errores.segundo_nombre" class="text-red-600 text-sm">{{ errores.segundo_nombre[0] }}
                        </p>
                    </div>

                    <div>
                        <label>Primer Apellido:</label>
                        <input v-model="form.primer_apellido" type="text" class="border w-full" />
                        <p v-if="errores.primer_apellido" class="text-red-600 text-sm">{{ errores.primer_apellido[0] }}
                        </p>
                    </div>

                    <div>
                        <label>Segundo Apellido:</label>
                        <input v-model="form.segundo_apellido" type="text" class="border w-full" />
                        <p v-if="errores.segundo_apellido" class="text-red-600 text-sm">{{ errores.segundo_apellido[0]
                        }}</p>
                    </div>

                    <div>
                        <label>Apellido de Casada:</label>
                        <input v-model="form.apellido_casada" type="text" class="border w-full" />
                        <p v-if="errores.apellido_casada" class="text-red-600 text-sm">{{ errores.apellido_casada[0] }}
                        </p>
                    </div>

                    <div>
                        <label>Tipo Documento:</label>
                        <input v-model="form.tipo_documento" type="text" class="border w-full" />
                        <p v-if="errores.tipo_documento" class="text-red-600 text-sm">{{ errores.tipo_documento[0] }}
                        </p>
                    </div>

                    <div>
                        <label>Pasaporte:</label>
                        <input v-model="form.pasaporte" type="text" class="border w-full" />
                        <p v-if="errores.pasaporte" class="text-red-600 text-sm">{{ errores.pasaporte[0] }}</p>
                    </div>

                    <div>
                        <label>CUI:</label>
                        <input v-model="form.cui" type="text" class="border w-full" />
                        <p v-if="errores.cui" class="text-red-600 text-sm">{{ errores.cui[0] }}</p>
                    </div>

                    <div>
                        <label>Fecha de Nacimiento:</label>
                        <input v-model="form.fecha_nacimiento" type="date" class="border w-full" />
                        <p v-if="errores.fecha_nacimiento" class="text-red-600 text-sm">{{ errores.fecha_nacimiento[0]
                        }}</p>
                    </div>

                    <div>
                        <label>Teléfono:</label>
                        <input v-model="form.telefono" type="text" class="border w-full" />
                        <p v-if="errores.telefono" class="text-red-600 text-sm">{{ errores.telefono[0] }}</p>
                    </div>

                    <div>
                        <label>Email:</label>
                        <input v-model="form.email" type="email" class="border w-full" />
                        <p v-if="errores.email" class="text-red-600 text-sm">{{ errores.email[0] }}</p>
                    </div>

                    <div>
                        <label>Sexo:</label>
                        <input v-model="form.sexo" type="text" class="border w-full" />
                        <p v-if="errores.sexo" class="text-red-600 text-sm">{{ errores.sexo[0] }}</p>
                    </div>

                    <div>
                        <label>Estado Civil:</label>
                        <input v-model="form.estado_civil" type="text" class="border w-full" />
                        <p v-if="errores.estado_civil" class="text-red-600 text-sm">{{ errores.estado_civil[0] }}</p>
                    </div>

                    <div>
                        <label>Dirección:</label>
                        <input v-model="form.direccion" type="text" class="border w-full" />
                        <p v-if="errores.direccion" class="text-red-600 text-sm">{{ errores.direccion[0] }}</p>
                    </div>
                </div>

                <div>
                    <label>Archivo (PDF, máx 1MB):</label>
                    <input type="file" @change="handleArchivo" accept="application/pdf" />
                    <p v-if="errores.archivo" class="text-red-600 text-sm">{{ errores.archivo[0] }}</p>
                </div>

                <div class="flex gap-4 mt-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Guardar Cambios</button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" @click="cancelarEdicion">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>


        <div v-if="rol === 'Usuario Normal'" class="mt-4">
            <a href="/personas/crear"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition-colors duration-200">
                Crear nuevo
            </a>
        </div>
    </div>
</template>
