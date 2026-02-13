<template>
	<section class="meet-portal">
		<header class="meet-portal__header">
			<h1>Portal do Cliente</h1>
			<p v-if="clientName">{{ clientName }}</p>
		</header>

		<p v-if="error" class="meet-portal__error">{{ error }}</p>
		<p v-if="loading" class="meet-portal__loading">Carregando dados do portal...</p>

		<div v-if="!loading" class="meet-portal__grid">
			<PhaseCard :phase="dashboard.client.phase" :progress="dashboard.client.progress" />
			<TasksList :tasks="dashboard.tasks" :pending-ids="pendingTaskIds" @complete="handleCompleteTask" />
			<FilesList :files="dashboard.files" />
		</div>
	</section>
</template>

<script setup>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { computed, onMounted, ref } from 'vue'
import FilesList from './FilesList.vue'
import PhaseCard from './PhaseCard.vue'
import TasksList from './TasksList.vue'

const loading = ref(true)
const error = ref('')
const pendingTaskIds = ref([])
const dashboard = ref({
	client: {
		name: '',
		phase: '',
		progress: 0,
	},
	tasks: [],
	files: [],
})

const clientName = computed(() => dashboard.value.client?.name || '')

const loadDashboard = async () => {
	loading.value = true
	error.value = ''
	try {
		const response = await axios.get(generateUrl('/apps/meet_portal/api/dashboard'))
		dashboard.value = response.data
	} catch (requestError) {
		error.value = 'Nao foi possivel carregar o dashboard.'
	} finally {
		loading.value = false
	}
}

const handleCompleteTask = async (taskId) => {
	if (pendingTaskIds.value.includes(taskId)) {
		return
	}

	pendingTaskIds.value.push(taskId)
	try {
		const response = await axios.post(generateUrl(`/apps/meet_portal/api/task/${taskId}/complete`))
		if (response.data?.success) {
			dashboard.value.tasks = dashboard.value.tasks.filter((task) => task.id !== taskId)
		}
	} catch (requestError) {
		error.value = 'Nao foi possivel concluir a tarefa.'
	} finally {
		pendingTaskIds.value = pendingTaskIds.value.filter((id) => id !== taskId)
	}
}

onMounted(loadDashboard)
</script>

<style scoped>
.meet-portal {
	padding: 24px;
}

.meet-portal__header {
	margin-bottom: 20px;
}

.meet-portal__header h1 {
	margin: 0;
	font-size: 30px;
}

.meet-portal__header p {
	margin: 6px 0 0;
	font-size: 16px;
	color: #6b7280;
}

.meet-portal__error {
	color: #b42318;
	margin-bottom: 12px;
}

.meet-portal__loading {
	margin-bottom: 12px;
}

.meet-portal__grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 16px;
}
</style>
