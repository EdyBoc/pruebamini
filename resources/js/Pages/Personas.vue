<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const personas = ref([])

const obtenerPersonas = async () => {
    const res = await axios.get('/api/personas')
    personas.value = res.data
}

const modoEdicion = ref(false)
const personaEditando = ref(null)

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


onMounted(() => {
    obtenerPersonas()
})

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
    direccion: '',
})


const archivo = ref(null)

const handleArchivo = (e) => {
    archivo.value = e.target.files[0]
}

const guardarPersona = async () => {
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
        console.error(error.response?.data || error)
        alert('Error al guardar persona')
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


</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Listado de Personas</h1>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Nombre</th>
                    <th class="border px-4 py-2">Apellido</th>
                    <th class="border px-4 py-2">CUI</th>
                    <th class="border px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in personas" :key="p.id">
                    <td class="border px-4 py-2">{{ p.primer_nombre }} {{ p.segundo_nombre }}</td>
                    <td class="border px-4 py-2">{{ p.primer_apellido }} {{ p.segundo_apellido }}</td>
                    <td class="border px-4 py-2">{{ p.cui }}</td>
                    <td class="border px-4 py-2">
                        <button class="bg-blue-500 text-white px-2 py-1 rounded mr-2"
                            @click="editarPersona(p)">Editar</button>

                        <button class="bg-red-600 text-white px-2 py-1 rounded"
                            @click="eliminarPersona(p.id)">Eliminar</button>

                        <button v-if="p.ruta_archivo" class="bg-indigo-600 text-white px-2 py-1 rounded mt-1"
                            @click="verArchivo(p.ruta_archivo)">
                            Ver PDF
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2 class="text-xl font-semibold mt-10 mb-4">Registrar Persona</h2>

    <form @submit.prevent="guardarPersona" class="space-y-4 bg-gray-100 p-4 rounded">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Primer Nombre:</label>
                <input v-model="form.primer_nombre" type="text" class="border w-full" required />
            </div>
            <div>
                <label>Segundo Nombre:</label>
                <input v-model="form.segundo_nombre" type="text" class="border w-full" />
            </div>

            <div>
                <label>Primer Apellido:</label>
                <input v-model="form.primer_apellido" type="text" class="border w-full" required />
            </div>
            <div>
                <label>Segundo Apellido:</label>
                <input v-model="form.segundo_apellido" type="text" class="border w-full" />
            </div>

            <div>
                <label>Apellido de Casada:</label>
                <input v-model="form.apellido_casada" type="text" class="border w-full" />
            </div>
            <div>
                <label>Tipo de Documento:</label>
                <select v-model="form.tipo_documento" class="border w-full" required>
                    <option value="">Seleccione</option>
                    <option value="CUI">CUI</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>

            <div>
                <label>Pasaporte:</label>
                <input v-model="form.pasaporte" type="text" class="border w-full" />
            </div>
            <div>
                <label>CUI:</label>
                <input v-model="form.cui" type="text" class="border w-full" required />
            </div>

            <div>
                <label>Fecha de Nacimiento:</label>
                <input v-model="form.fecha_nacimiento" type="date" class="border w-full" required />
            </div>
            <div>
                <label>Teléfono:</label>
                <input v-model="form.telefono" type="text" class="border w-full" required />
            </div>

            <div>
                <label>Email:</label>
                <input v-model="form.email" type="email" class="border w-full" required />
            </div>
            <div>
                <label>Sexo:</label>
                <select v-model="form.sexo" class="border w-full" required>
                    <option value="">Seleccione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div>
                <label>Estado Civil:</label>
                <input v-model="form.estado_civil" type="text" class="border w-full" required />
            </div>
            <div>
                <label>Dirección:</label>
                <textarea v-model="form.direccion" class="border w-full" required></textarea>
            </div>
        </div>

        <div class="mt-4">
            <label>Archivo (PDF, máx 1MB):</label>
            <input type="file" @change="handleArchivo" accept="application/pdf" />
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 mt-4 rounded">
            Guardar
        </button>
        <button v-if="modoEdicion" type="button" class="bg-gray-400 text-white px-4 py-2 mt-2 rounded ml-2"
            @click="cancelarEdicion">
            Cancelar Edición
        </button>

    </form>


</template>
