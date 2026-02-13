<template>
	<article class="card">
		<h2>Documentos Principais</h2>
		<ul v-if="files.length" class="files">
			<li v-for="file in files" :key="file.name" class="file">
				<span>{{ file.name }}</span>
				<a :href="file.url" class="file__link" target="_blank" rel="noopener noreferrer">Abrir</a>
			</li>
		</ul>
		<p v-else>Nenhum documento encontrado.</p>
	</article>
</template>

<script setup>
defineProps({
	files: {
		type: Array,
		default: () => [],
	},
})
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

.files {
	list-style: none;
	margin: 0;
	padding: 0;
	display: grid;
	gap: 8px;
}

.file {
	display: flex;
	justify-content: space-between;
	border: 1px solid #e5e7eb;
	border-radius: 8px;
	padding: 10px;
}

.file__link {
	font-weight: 600;
	color: #0b6acb;
	text-decoration: none;
}

.file__link:hover {
	text-decoration: underline;
}
</style>
