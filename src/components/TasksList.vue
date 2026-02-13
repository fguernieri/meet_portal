<template>
	<article class="card">
		<h2>Tarefas Pendentes</h2>
		<ul v-if="tasks.length" class="tasks">
			<li v-for="task in tasks" :key="task.id" class="task">
				<div class="task__content">
					<p class="task__title">{{ task.title }}</p>
					<p class="task__date">Entrega: {{ task.due_date }}</p>
				</div>
				<button type="button" class="task__button" :disabled="isPending(task.id)" @click="$emit('complete', task.id)">
					{{ isPending(task.id) ? 'Salvando...' : 'Marcar como concluida' }}
				</button>
			</li>
		</ul>
		<p v-else>Nenhuma tarefa pendente.</p>
	</article>
</template>

<script setup>
const props = defineProps({
	tasks: {
		type: Array,
		default: () => [],
	},
	pendingIds: {
		type: Array,
		default: () => [],
	},
})

defineEmits(['complete'])

const isPending = (taskId) => props.pendingIds.includes(taskId)
</script>

<style scoped>
.card {
	background: #fff;
	border-radius: 12px;
	border: 1px solid #d8dee4;
	padding: 18px;
}

h2 {
	font-size: 18px;
	margin: 0 0 12px;
}

.tasks {
	list-style: none;
	margin: 0;
	padding: 0;
	display: grid;
	gap: 10px;
}

.task {
	display: flex;
	justify-content: space-between;
	gap: 12px;
	align-items: center;
	border: 1px solid #e5e7eb;
	border-radius: 8px;
	padding: 10px;
}

.task__title {
	margin: 0;
	font-weight: 600;
}

.task__date {
	margin: 4px 0 0;
	color: #6b7280;
	font-size: 13px;
}

.task__button {
	white-space: nowrap;
	border-radius: 8px;
	border: none;
	background: #0b6acb;
	color: #fff;
	font-weight: 600;
	padding: 8px 12px;
	cursor: pointer;
}

.task__button:disabled {
	opacity: 0.7;
	cursor: wait;
}
</style>
