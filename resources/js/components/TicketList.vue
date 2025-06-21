<template>
  <div class="ticket-list">
    <div class="ticket-list__header">
      <div class="ticket-list__header-left">
        <h1 class="ticket-list__heading">All Tickets</h1>
        <div v-if="unclassifiedCount > 0" class="ticket-list__unclassified-info">
          <span class="ticket-list__unclassified-count">{{ unclassifiedCount }} tickets need classification</span>
        </div>
        <!-- <div v-if="queueStatus" class="ticket-list__queue-status">
          <span :class="['queue-indicator', queueStatus.queue_working ? 'queue-working' : 'queue-idle']">
            <i :class="queueStatus.queue_working ? 'pi pi-check-circle' : 'pi pi-exclamation-triangle'"></i>
            {{ queueStatus.queue_working ? 'Queue Active' : 'Queue Idle' }}
            <span v-if="queueStatus.pending_jobs > 0">({{ queueStatus.pending_jobs }} pending)</span>
            <span v-if="queueStatus.failed_jobs > 0" class="failed-jobs">({{ queueStatus.failed_jobs }} failed)</span>
          </span>
        </div> -->
      </div>
      <div class="ticket-list__header-actions">
        <button 
          v-if="unclassifiedCount > 0"
          class="btn btn--secondary ticket-list__bulk-btn"
          :disabled="bulkLoading"
          @click="bulkClassify"
        >
          <i v-if="bulkLoading" class="pi pi-spin pi-spinner"></i>
          <span v-else><i class="pi pi-check-square"></i> Bulk Classify</span>
        </button>
        <router-link to="/" class="btn btn--primary ticket-list__new-ticket-btn">
          <i class="pi pi-plus"></i> New Ticket
        </router-link>
      </div>
    </div>

    <!-- Filters -->
    <div class="ticket-list__filters">
      <input
        v-model="search"
        @input="fetchTickets"
        class="ticket-list__search"
        type="text"
        placeholder="Search subject/body"
      />

      <select v-model="filterStatus" @change="fetchTickets" class="ticket-list__filter">
        <option value="">All Status</option>
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="resolved">Resolved</option>
      </select>

      <select v-model="filterCategory" @change="fetchTickets" class="ticket-list__filter">
        <option value="">All Categories</option>
        <option value="billing">Billing</option>
        <option value="technical">Technical</option>
        <option value="account">Account</option>
        <option value="unclassified">Unclassified</option>
      </select>
    </div>

    <!-- Ticket Table -->
    <div class="ticket-list__table-wrapper">
      <table class="ticket-list__table">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Status</th>
            <th>Category</th>
            <th>Confidence</th>
            <!-- <th>Explanation</th> -->
            <th>Note</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in tickets" :key="ticket.id" class="ticket-list__item">
            <td>{{ ticket.subject }}</td>
            <td>
              <span :class="['badge', 'badge--status', ticket.status]">
                {{ ticket.status.replace('_', ' ').toUpperCase() }}
              </span>
            </td>
            <td>
              <span :class="['badge', 'badge--category', ticket.category || 'unclassified']">
                {{ ticket.category || 'Unclassified' }}
              </span>
            </td>
            <td>{{ ticket.confidence ? ticket.confidence.toFixed(2) : '-' }}</td>
            <!-- <td>
              <span v-if="ticket.explanation" class="ticket-list__info" :title="ticket.explanation"><i class="pi pi-info-circle"></i></span>
              <span v-else>-</span>
            </td> -->
            <td>
              <span v-if="ticket.note" class="ticket-list__badge"> {{ticket.note}}</span>
              <span v-else>-</span>
            </td>
            <td>
              <button
                class="btn btn--primary"
                :disabled="loadingIds.includes(ticket.id) || (ticket.category && ticket.category !== 'unclassified')"
                @click="classify(ticket.id)"
              >
                <i v-if="loadingIds.includes(ticket.id)" class="pi pi-spin pi-spinner"></i>
                <i v-else-if="ticket.category && ticket.category !== 'unclassified'" class="pi pi-check tick-icon"></i>
                <span v-else><i class="pi pi-check"></i> Classify</span>
              </button>
              <router-link :to="'/tickets/' + ticket.id" class="btn btn--secondary"><i class="pi pi-search"></i> Details</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="ticket-list__pagination">
      <button class="btn btn--secondary" :disabled="!prevUrl" @click="changePage(prevUrl)"><i class="pi pi-angle-left"></i> Previous</button>
      <button class="btn btn--secondary" :disabled="!nextUrl" @click="changePage(nextUrl)">Next <i class="pi pi-angle-right"></i></button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TicketList",
  data() {
    return {
      tickets: [],
      search: "",
      filterStatus: "",
      filterCategory: "",
      nextUrl: null,
      prevUrl: null,
      loadingIds: [],
      unclassifiedCount: 0,
      bulkLoading: false,
      pollingIntervals: {},
      queueStatus: null,
    };
  },
  created() {
    this.fetchTickets();
    this.fetchStats();
    this.fetchQueueStatus();
  },
  beforeUnmount() {
    this.clearAllPolling();
  },
  methods: {
    async fetchTickets(url = "/api/tickets") {
      if (!url || typeof url !== 'string') {
        url = "/api/tickets";
      }
      const params = {
        search: this.search,
        status: this.filterStatus,
        category: this.filterCategory,
      };
      const response = await axios.get(url, { params });
      this.tickets = response.data.data;
      this.nextUrl = response.data.next_page_url;
      this.prevUrl = response.data.prev_page_url;
    },
    async fetchStats() {
      try {
        const response = await axios.get('/api/stats');
        this.unclassifiedCount = response.data.unclassified_count || 0;
      } catch (err) {
        console.error('Failed to fetch stats:', err);
      }
    },
    async fetchQueueStatus() {
      try {
        const response = await axios.get('/api/queue-status');
        this.queueStatus = response.data;
      } catch (err) {
        console.error('Failed to fetch queue status:', err);
      }
    },
    async classify(id) {
      if (this.loadingIds.includes(id)) return;
      this.loadingIds.push(id);
      
      try {
        const response = await axios.post(`/api/tickets/${id}/classify`);
        
        if (response.data.status === 'queued') {
          this.startPollingForTicket(id);
        } else {
          throw new Error(response.data.message || 'Classification failed');
        }
      } catch (err) {
        console.error('Classification error:', err);
        alert(`Failed to classify ticket: ${err.response?.data?.message || err.message}`);
        this.loadingIds = this.loadingIds.filter((tid) => tid !== id);
      }
    },
    async checkTicketStatus(id) {
      try {
        const response = await axios.get(`/api/tickets/${id}/classification-status`);
        const ticketData = response.data;
        
        if (ticketData.is_classified) {
          this.stopPollingForTicket(id);
          this.loadingIds = this.loadingIds.filter((tid) => tid !== id);
          await this.fetchTickets();
          await this.fetchStats();
        }
      } catch (err) {
        console.error('Status check error:', err);
        this.stopPollingForTicket(id);
        this.loadingIds = this.loadingIds.filter((tid) => tid !== id);
        alert('Failed to check classification status');
      }
    },
    startPollingForTicket(id) {
      this.stopPollingForTicket(id);
      this.pollingIntervals[id] = setInterval(() => {
        this.checkTicketStatus(id);
      }, 2000);
    },
    stopPollingForTicket(id) {
      if (this.pollingIntervals[id]) {
        clearInterval(this.pollingIntervals[id]);
        delete this.pollingIntervals[id];
      }
    },
    clearAllPolling() {
      Object.keys(this.pollingIntervals).forEach(id => {
        this.stopPollingForTicket(id);
      });
    },
    changePage(url) {
      if (!url || typeof url !== 'string') return;
      this.fetchTickets(url);
    },
    async bulkClassify() {
      this.bulkLoading = true;
      try {
        const response = await axios.post('/api/tickets/bulk-classify');
        
        if (response.data.status === 'queued') {
          alert(`Bulk classification queued for ${response.data.tickets_queued} tickets. They will be processed in the background.`);
          await this.fetchTickets();
          await this.fetchStats();
        } else if (response.data.status === 'no_tickets') {
          alert('No unclassified tickets found to process.');
        } else {
          throw new Error(response.data.message || 'Bulk classification failed');
        }
      } catch (err) {
        console.error('Bulk classification error:', err);
        alert(`Failed to bulk classify tickets: ${err.response?.data?.message || err.message}`);
      } finally {
        this.bulkLoading = false;
      }
    },
  },
};
</script>

<style scoped>
.ticket-list {
  padding: 1.5rem 0.5rem;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  color: var(--color-text);
}
.ticket-list__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}
.ticket-list__header-left {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.ticket-list__header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}
.ticket-list__heading {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
  color: var(--color-primary);
  text-align: center;
}
.ticket-list__filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  justify-content: center;
}
.ticket-list__search,
.ticket-list__filter {
  padding: 0.5rem 1rem;
  font-size: 1rem;
  border-radius: 8px;
  border: 1px solid var(--color-secondary);
  background: var(--color-accent);
  color: var(--color-primary);
  outline: none;
  transition: border 0.2s;
}
.ticket-list__search:focus,
.ticket-list__filter:focus {
  border: 1.5px solid var(--color-primary);
}
.ticket-list__table-wrapper {
  background: var(--color-white);
  border-radius: 1.5rem;
  box-shadow: 0 4px 24px var(--color-shadow);
  overflow-x: auto;
  margin-bottom: 1.5rem;
  max-width: 100vw;
}
.ticket-list__table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  min-width: 700px;
}
.ticket-list__table th,
.ticket-list__table td {
  padding: 0.9rem 0.7rem;
  border-bottom: 1px solid var(--color-accent);
  text-align: left;
  font-size: 1rem;
}
.ticket-list__table th {
  background: var(--color-secondary);
  color: var(--color-white);
  font-weight: 700;
}
.ticket-list__item {
  transition: background 0.2s;
}
.ticket-list__item:hover {
  background: var(--color-accent);
}
.badge {
  display: inline-block;
  padding: 0.15em 0.9em;
  border-radius: 1.5em;
  font-size: 0.85em;
  box-shadow: 0 1px 4px var(--color-shadow);
  margin-right: 0.2em;
}
.badge--status.open {
  background: var(--color-secondary);
  color: var(--color-white);
}
.badge--status.in_progress {
  background: #FFD600;
  color: var(--color-primary);
  width: max-content;
}
.badge--status.resolved {
  background: #16a34a;
  color: var(--color-white);
}
.badge--category.billing {
  background: var(--color-primary);
  color: var(--color-white);
}
.badge--category.technical {
  background: var(--color-secondary);
  color: var(--color-white);
}
.badge--category.account {
  background: var(--color-accent);
  color: var(--color-primary);
}
.badge--category.unclassified {
  background: #eee;
  color: #aaa;
  font-style: italic;
}
.ticket-list__info {
  cursor: help;
  color: var(--color-primary);
}
.ticket-list__info i {
  font-size: 1.1rem;
  vertical-align: middle;
}
.ticket-list__badge {
  font-size: 0.8rem;
}
.ticket-list__badge i {
  font-size: 1.1rem;
  vertical-align: middle;
}
.tick-icon {
  color: #16a34a;
  font-size: 1.2rem;
}
.btn {
  display: inline-block;
  padding: 0.45rem 1.1rem;
  border-radius: 8px;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  margin: 0.1rem 0.2rem;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px var(--color-shadow);
}
.btn--primary {
  background: var(--color-primary);
  color: var(--color-white);
  width: 122px;
  height: 42px;
}
.btn--primary:disabled {
  background: var(--color-secondary);
  color: var(--color-white);
  cursor: wait;
}
.btn--primary:hover:not(:disabled) {
  background: var(--color-secondary);
  color: var(--color-white);
}
.btn--secondary {
  background: var(--color-accent);
  color: var(--color-primary);
  border: 1.5px solid var(--color-secondary);
  text-decoration: none;
}
.btn--secondary:hover:not(:disabled) {
  background: var(--color-secondary);
  color: var(--color-white);
}
.btn--secondary i {
  margin-left: 0.4em;
  font-size: 1.1em;
  vertical-align: middle;
}
.ticket-list__pagination {
  margin-top: 1rem;
  display: flex;
  gap: 1rem;
  justify-content: center;
}
.ticket-list__new-ticket-btn {
  font-size: 1rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  height: 42px;
  padding: 0 1.2rem;
  text-decoration: none;
}
.ticket-list__unclassified-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.ticket-list__unclassified-count {
  font-size: 0.8rem;
  color: var(--color-white);
  background: #16a34a;
  padding: 0.2rem 0.5rem;
  border-radius: 1.5em;
  border-radius: 20px;
}
.ticket-list__bulk-btn {
  background: var(--color-accent);
  color: var(--color-primary);
  border: 1.5px solid var(--color-secondary);
  text-decoration: none;
}
.ticket-list__bulk-btn:hover:not(:disabled) {
  background: var(--color-secondary);
  color: var(--color-white);
}
.ticket-list__bulk-btn:disabled {
  background: var(--color-secondary);
  color: var(--color-white);
  cursor: wait;
}
.ticket-list__bulk-btn i {
  margin-right: 0.4em;
  font-size: 1.1em;
  vertical-align: middle;
}
.ticket-list__queue-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.queue-indicator {
  display: flex;
  align-items: center;
  gap: 0.2rem;
  padding: 0.2rem 0.5rem;
  border-radius: 1.5em;
  border: 1px solid var(--color-secondary);
  background: var(--color-accent);
}
.queue-indicator i {
  font-size: 1.2rem;
  vertical-align: middle;
}
.queue-working {
  background: #16a34a;
  color: var(--color-white);
}
.queue-idle {
  background: #FFD600;
  color: var(--color-primary);
}
.failed-jobs {
  background: #FF0000;
  color: var(--color-white);
  padding: 0.2rem 0.5rem;
  border-radius: 1.5em;
}
@media (max-width: 900px) {
  .ticket-list__table-wrapper {
    border-radius: 1rem;
  }
  .ticket-list__heading {
    font-size: 1.3rem;
  }
}
@media (max-width: 600px) {
  .ticket-list__table-wrapper {
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px var(--color-shadow);
    max-width: 100vw;
    overflow-x: auto;
  }
  .ticket-list__table th,
  .ticket-list__table td {
    padding: 0.5rem 0.3rem;
    font-size: 0.95rem;
  }
  .ticket-list__filters {
    flex-direction: column;
    align-items: stretch;
  }
  .ticket-list__scroll-hint {
    text-align: center;
    font-size: 0.9rem;
    color: var(--color-primary);
    margin-bottom: 0.5rem;
    margin-top: -1rem;
  }
  .ticket-list__header {
    flex-direction: column;
    align-items: stretch;
    gap: 0.7rem;
  }
  .ticket-list__header-left {
    text-align: center;
  }
  .ticket-list__header-actions {
    flex-direction: column;
    align-items: stretch;
  }
  .ticket-list__new-ticket-btn {
    width: 100%;
    justify-content: center;
  }
  .ticket-list__bulk-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
