<template>
	<article class="card">
		<h2>Fase Atual</h2>
		<p class="phase">{{ phase || '-' }}</p>
		<div class="progress">
			<div class="progress__bar" :style="{ width: normalizedProgress + '%' }"></div>
		</div>
		<p class="progress__value">{{ normalizedProgress }}%</p>
	</article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	phase: {
		type: String,
		default: '',
	},
	progress: {
		type: Number,
		default: 0,
	},
})

const normalizedProgress = computed(() => Math.max(0, Math.min(100, props.progress)))
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
	margin: 0 0 8px;
}

.phase {
	font-size: 22px;
	margin: 0 0 12px;
}

.progress {
	height: 12px;
	background: #eef2f6;
	border-radius: 999px;
	overflow: hidden;
}

.progress__bar {
	height: 100%;
	background: #0b6acb;
	transition: width 200ms ease;
}

.progress__value {
	margin: 8px 0 0;
	font-weight: 600;
}
</style>
