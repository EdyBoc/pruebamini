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

    <form @submit.prevent="guardarPersona" class="space-y-4 bg-gray-100 p-4 rounded" style="width: 60%; margin: auto; margin-top: 50px;">
    <h1 class="text-2xl font-bold mb-4">Registrar Persona</h1>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Primer Nombre:</label>
                <input v-model="form.primer_nombre" type="text" class="border w-full" />
                <p v-if="errores.primer_nombre" class="text-red-600 text-sm">{{ errores.primer_nombre[0] }}</p>
            </div>

            <div>
                <label>Segundo Nombre:</label>
                <input v-model="form.segundo_nombre" type="text" class="border w-full" />
                <p v-if="errores.segundo_nombre" class="text-red-600 text-sm">{{ errores.segundo_nombre[0] }}</p>
            </div>

            <div>
                <label>Primer Apellido:</label>
                <input v-model="form.primer_apellido" type="text" class="border w-full" />
                <p v-if="errores.primer_apellido" class="text-red-600 text-sm">{{ errores.primer_apellido[0] }}</p>
            </div>

            <div>
                <label>Segundo Apellido:</label>
                <input v-model="form.segundo_apellido" type="text" class="border w-full" />
                <p v-if="errores.segundo_apellido" class="text-red-600 text-sm">{{ errores.segundo_apellido[0] }}</p>
            </div>

            <div>
                <label>Apellido de Casada:</label>
                <input v-model="form.apellido_casada" type="text" class="border w-full" />
                <p v-if="errores.apellido_casada" class="text-red-600 text-sm">{{ errores.apellido_casada[0] }}</p>
            </div>

            <div>
                <label>Tipo de Documento:</label>
                <select v-model="form.tipo_documento" class="border w-full">
                    <option value="">Seleccione</option>
                    <option value="CUI">CUI</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
                <p v-if="errores.tipo_documento" class="text-red-600 text-sm">{{ errores.tipo_documento[0] }}</p>
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
                <p v-if="errores.fecha_nacimiento" class="text-red-600 text-sm">{{ errores.fecha_nacimiento[0] }}</p>
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
                <select v-model="form.sexo" class="border w-full">
                    <option value="">Seleccione</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
                <p v-if="errores.sexo" class="text-red-600 text-sm">{{ errores.sexo[0] }}</p>
            </div>

            <div>
                <label>Estado Civil:</label>
                <input v-model="form.estado_civil" type="text" class="border w-full" />
                <p v-if="errores.estado_civil" class="text-red-600 text-sm">{{ errores.estado_civil[0] }}</p>
            </div>

            <div>
                <label>Dirección:</label>
                <textarea v-model="form.direccion" class="border w-full"></textarea>
                <p v-if="errores.direccion" class="text-red-600 text-sm">{{ errores.direccion[0] }}</p>
            </div>
        </div>

        <div class="mt-4">
            <label>Archivo (PDF, máx 1MB):</label>
            <input type="file" @change="handleArchivo" accept="application/pdf" />
            <p v-if="errores.archivo" class="text-red-600 text-sm">{{ errores.archivo[0] }}</p>
        </div>



        <div class="mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 mt-4 rounded">Guardar</button>
            &nbsp;
            <a href="/personas/ver"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition-colors duration-200">
                Ver listado de personas
            </a>
        </div>
    </form>
</template>
