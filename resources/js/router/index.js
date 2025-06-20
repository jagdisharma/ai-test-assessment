import { createRouter, createWebHistory } from 'vue-router'

import TicketList from '../views/TicketList.vue'
import TicketDetail from '../views/TicketDetail.vue'
import TicketForm from '../views/TicketForm.vue'
import Dashboard from '../views/Dashboard.vue'
import SuccessPage from '../views/SuccessPage.vue'

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: TicketForm },
        { path: '/tickets', component: TicketList },
        { path: '/tickets/:id', component: TicketDetail , props: true },
        { path: '/dashboard', component: Dashboard },
        { path: '/ticket-success', component: SuccessPage },
    ]
})
