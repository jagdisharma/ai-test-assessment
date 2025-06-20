<template>
  <div class="ticket-list">
    <h1 class="ticket-list__heading">All Tickets</h1>

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
    <table class="ticket-list__table">
      <thead>
        <tr>
          <th>Subject</th>
          <th>Status</th>
          <th>Category</th>
          <th>Confidence</th>
          <th>Explanation</th>
          <th>Note</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ticket in tickets" :key="ticket.id" class="ticket-list__item">
          <td>{{ ticket.subject }}</td>
          <td>{{ ticket.status }}</td>
          <td>
            <span :class="['ticket-list__category', ticket.category || 'unclassified']">
              {{ ticket.category || 'Unclassified' }}
            </span>
          </td>
          <td>{{ ticket.confidence ? ticket.confidence.toFixed(2) : '-' }}</td>
          <td>
            <span v-if="ticket.explanation" class="ticket-list__info" :title="ticket.explanation">🛈</span>
            <span v-else>-</span>
          </td>
          <td>
            <span v-if="ticket.note" class="ticket-list__badge">📝</span>
            <span v-else>-</span>
          </td>
          <td>
            <button
              class="ticket-list__classify-btn"
              :disabled="loadingIds.includes(ticket.id)"
              @click="classify(ticket.id)"
            >
              <span v-if="loadingIds.includes(ticket.id)">⏳</span>
              <span v-else>Classify</span>
            </button>
            <router-link :to="'/tickets/' + ticket.id">Details</router-link>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="ticket-list__pagination">
      <button :disabled="!prevUrl" @click="changePage(prevUrl)">Previous</button>
      <button :disabled="!nextUrl" @click="changePage(nextUrl)">Next</button>
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
    };
  },
  created() {
    this.fetchTickets();
  },
  methods: {
    async fetchTickets(url = "/api/tickets") {

        if (!url || typeof url !== 'string') {
            url = "/api/tickets";
        }

      const params = {
        search: this.search,
        status: this.filterStatus,
        category: this.filterCategory === "unclassified" ? "" : this.filterCategory,
      };

      const response = await axios.get(url, { params });
console.log(response);
      this.tickets = response.data.data;
      this.nextUrl = response.data.next_page_url;
      this.prevUrl = response.data.prev_page_url;
    },
    async classify(id) {
      if (this.loadingIds.includes(id)) return;

      this.loadingIds.push(id);
      try {
        await axios.post(`/api/tickets/${id}/classify`);
        await this.fetchTickets();
      } catch (err) {
        alert("Failed to classify ticket.");
      } finally {
        this.loadingIds = this.loadingIds.filter((tid) => tid !== id);
      }
    },
    changePage(url) {
   
     if (!url || typeof url !== 'string') return;
        this.fetchTickets(url);
    },
  },
};
</script>

<style scoped>
.ticket-list {
  padding: 1rem;
}
.ticket-list__heading {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
.ticket-list__filters {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.ticket-list__search,
.ticket-list__filter {
  padding: 0.4rem 0.6rem;
  font-size: 1rem;
}
.ticket-list__table {
  width: 100%;
  border-collapse: collapse;
}
.ticket-list__table th,
.ticket-list__table td {
  padding: 0.6rem;
  border: 1px solid #ddd;
  text-align: left;
}
.ticket-list__category.unclassified {
  color: #aaa;
  font-style: italic;
}
.ticket-list__info {
  cursor: help;
  color: #666;
}
.ticket-list__badge {
  font-size: 1.2rem;
}
.ticket-list__classify-btn {
  padding: 0.3rem 0.6rem;
  background: #007bff;
  border: none;
  color: white;
  cursor: pointer;
}
.ticket-list__classify-btn:disabled {
  background: #aaa;
  cursor: wait;
}
.ticket-list__pagination {
  margin-top: 1rem;
  display: flex;
  gap: 1rem;
}
</style>
