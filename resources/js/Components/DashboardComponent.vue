<template>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/images/chaos-planner-logo.png" alt="Chaos Planner Logo">
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#" :class="{ active: activeMenu === 'dashboard' }" @click.prevent="activeMenu = 'dashboard'">Личный кабинет</a></li>
                <li><a href="#" :class="{ active: activeMenu === 'history' }" @click.prevent="activeMenu = 'history'">История</a></li>
                <li><a href="#" :class="{ active: activeMenu === 'calendar' }" @click.prevent="activeMenu = 'calendar'">Календарь</a></li>
                <li><a href="#" :class="{ active: activeMenu === 'telegram' }" @click.prevent="activeMenu = 'telegram'">Телеграм-бот</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="card grid-span-2"> <!-- Сделаем этот блок на всю ширину при необходимости -->
            <h2>Привет, {{ userName }}!</h2>
            <p>Сегодня, {{ currentDate }}. Отличный день для продуктивности!</p>
        </div>

        <div class="card">
            <h2>Задачи</h2>
            <ul class="task-list">
                <li v-for="task in tasks" :key="task.id" class="task-item">
                    <span class="icon">⚪</span> <!-- Иконка для задачи -->
                    {{ task.title }}
                    <small v-if="task.due_date"> (до {{ formatTime(task.due_date) }})</small>
                </li>
                <li v-if="tasks.length === 0" class="task-item">Нет активных задач.</li>
            </ul>
        </div>

        <div class="card">
            <h2>Привычки</h2>
            <ul class="habit-list">
                <li v-for="habit in habits" :key="habit.id" class="habit-item">
                    <span class="icon" v-if="habit.completed_today">✅</span>
                    <span class="icon" v-else>⬜</span>
                    {{ habit.title }}
                </li>
                <li v-if="habits.length === 0" class="habit-item">Нет привычек для отслеживания.</li>
            </ul>
        </div>

        <div class="card statistic-card">
            <h2>Краткая статистика</h2>
            <div class="statistic-value">{{ statistics.success_percentage }}%</div>
            <div class="statistic-label">Процент выполнения за неделю</div>

            <div class="statistic-detail">
                <span>Выполнено задач:</span>
                <span>{{ statistics.tasks_completed }}</span>
            </div>
            <div class="statistic-detail">
                <span>Сформировано привычек:</span>
                <span>{{ statistics.habits_completed }}</span>
            </div>
            <!-- Прогресс-бар для статистики, если нужно -->
            <div class="progress-bar">
                <div class="progress-fill" :style="{ width: statistics.success_percentage + '%' }"></div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { format } from 'date-fns';
import { ru } from 'date-fns/locale';

export default {
    data() {
        return {
            userName: 'Иван', // По умолчанию, будет загружено из API
            currentDate: '',
            activeMenu: 'dashboard',
            tasks: [],
            habits: [],
            statistics: {
                tasks_completed: 0,
                habits_completed: 0,
                success_percentage: 0
            }
        };
    },
    methods: {
        async fetchDashboardData() {
            try {
                const response = await axios.get('/api/dashboard-data');
                const data = response.data;
                this.userName = data.user.name;
                this.tasks = data.tasks;
                this.habits = data.habits;
                this.statistics = data.statistics;
            } catch (error) {
                console.error("Ошибка при загрузке данных дашборда:", error);
                // Обработка ошибок, например, перенаправление на страницу входа, если нет авторизации
            }
        },
        formatTime(dateString) {
            if (!dateString) return '';
            return format(new Date(dateString), 'HH:mm', { locale: ru });
        }
    },
    created() {
        this.currentDate = format(new Date(), 'dd MMMM yyyy г.', { locale: ru });
        this.fetchDashboardData();
    }
};
</script>

<style scoped>
/* Стили из Blade-файла можно перенести сюда, если вы хотите инкапсулировать их в компоненте Vue */
/* Или оставить в Blade, если они общие для всего приложения */

/* Пример некоторых специфичных для компонента стилей, если они будут */
.main-content {
    grid-template-columns: 2fr 1fr; /* Адаптируем под ваш макет */
    grid-template-rows: auto auto auto;
}
.card.statistic-card {
    grid-column: 2 / 3; /* Размещаем статистику во втором столбце */
    grid-row: 1 / 3; /* Занимает 2 ряда */
    display: flex;
    flex-direction: column;
    justify-content: center; /* Центрируем содержимое */
    align-items: center;
}
.card h2 {
    width: 100%; /* Заголовок занимает всю ширину карточки */
    text-align: left;
}
.statistic-value, .statistic-label, .statistic-detail {
    width: 100%; /* Для центрирования */
    max-width: 250px; /* Ограничим ширину для лучшего вида */
}
.statistic-detail {
    border-top: none; /* Убираем полоску между деталями */
    padding-top: 5px;
    padding-bottom: 5px;
}
.statistic-card .progress-bar {
    width: 80%; /* Прогресс-бар занимает большую часть ширины */
    margin-top: 20px;
}

</style>