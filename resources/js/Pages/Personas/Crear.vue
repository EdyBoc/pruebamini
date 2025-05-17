<script setup>
import { ref } from 'vue'
import axios from 'axios'

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
const errores = ref({})

const handleArchivo = (e) => {
    archivo.value = e.target.files[0]
}

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

        await axios.post('/api/personas', data)
        alert('Persona registrada correctamente')
        window.location.href = '/personas/ver'
    } catch (error) {
        if (error.response?.status === 422) {
            errores.value = error.response.data.errors
        } else {
            alert('Error inesperado al guardar persona')
            console.error(error)
        }
    }
}
</script>

<template>
    <form @submit.prevent="guardarPersona" class="bg-white p-8 rounded shadow-md max-w-xl mx-auto mt-10 space-y-6">
        <h1 class="text-3xl font-bold text-center text-blue-800 mb-6">Registro de Personal</h1>

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
            <label class="block text-gray-700 font-semibold mb-1">Tipo de Documento:</label>
            <select v-model="form.tipo_documento" class="border w-full p-2 rounded">
                <option value="">Seleccione</option>
                <option value="CUI">CUI</option>
                <option value="Pasaporte">Pasaporte</option>
            </select>
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
            <select v-model="form.sexo" class="border w-full p-2 rounded">
                <option value="">Seleccione</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
            <p v-if="errores.sexo" class="text-red-600 text-sm">{{ errores.sexo[0] }}</p>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Estado Civil:</label>
            <input v-model="form.estado_civil" type="text" class="border w-full p-2 rounded" />
            <p v-if="errores.estado_civil" class="text-red-600 text-sm">{{ errores.estado_civil[0] }}</p>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Dirección:</label>
            <textarea v-model="form.direccion" class="border w-full p-2 rounded"></textarea>
            <p v-if="errores.direccion" class="text-red-600 text-sm">{{ errores.direccion[0] }}</p>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold mb-1">Archivo (PDF, máx 1MB):</label>
            <input type="file" @change="handleArchivo" accept="application/pdf" class="w-full" />
            <p v-if="errores.archivo" class="text-red-600 text-sm">{{ errores.archivo[0] }}</p>
        </div>

        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                Guardar
            </button>
            <a href="/personas/ver" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                Ver listado
            </a>
        </div>
    </form>
</template>
