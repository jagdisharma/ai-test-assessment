<template>
  <div class="ticket-detail" v-if="ticket">
    <h2 class="ticket-detail__heading">{{ ticket.subject }}</h2>
    <p class="ticket-detail__body">{{ ticket.body }}</p>

    <div class="ticket-detail__form">
      <!-- Category Dropdown -->
      <label class="ticket-detail__label">Category</label>
      <select v-model="ticket.category" @change="updateField('category', ticket.category)" class="ticket-detail__select">
        <option value="">Unclassified</option>
        <option value="billing">Billing</option>
        <option value="technical">Technical</option>
        <option value="account">Account</option>
      </select>

      <!-- Note Textarea -->
      <label class="ticket-detail__label">Internal Note</label>
      <textarea
        v-model="ticket.note"
        @blur="updateField('note', ticket.note)"
        class="ticket-detail__textarea"
        placeholder="Add your internal note here"
      ></textarea>

      <!-- Explanation & Confidence -->
      <div class="ticket-detail__readonly">
        <p><strong>AI Explanation:</strong> {{ ticket.explanation || '-' }}</p>
        <p><strong>Confidence:</strong> {{ ticket.confidence ? ticket.confidence.toFixed(2) : '-' }}</p>
      </div>

      <!-- Run Classification Button -->
      <button @click="runClassification" :disabled="loading" class="ticket-detail__button">
        <span v-if="loading">⏳ Classifying...</span>
        <span v-else>Run Classification</span>
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TicketDetail",
  props: {
    id: { type: String, required: true },
  },
  data() {
    return {
      ticket: null,
      loading: false,
    };
  },
  created() {
    this.fetchTicket();
  },
  methods: {
    async fetchTicket() {
      const response = await axios.get(`/api/tickets/${this.id}`);
      this.ticket = response.data;
    },
    async updateField(field, value) {
      try {
        await axios.patch(`/api/tickets/${this.id}`, { [field]: value });
      } catch (error) {
        alert("Update failed!");
      }
    },
    async runClassification() {
      this.loading = true;
      try {
        await axios.post(`/api/tickets/${this.id}/classify`);
        await this.fetchTicket();
      } catch (error) {
        alert("Classification failed!");
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.ticket-detail {
  padding: 1rem;
}
.ticket-detail__heading {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}
.ticket-detail__body {
  font-size: 1rem;
  margin-bottom: 1rem;
}
.ticket-detail__form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.ticket-detail__label {
  font-weight: bold;
}
.ticket-detail__select,
.ticket-detail__textarea {
  padding: 0.5rem;
  font-size: 1rem;
}
.ticket-detail__textarea {
  min-height: 100px;
}
.ticket-detail__readonly {
  background: #f9f9f9;
  padding: 0.5rem;
  border-left: 4px solid #ccc;
}
.ticket-detail__button {
  padding: 0.6rem 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  font-weight: bold;
  cursor: pointer;
}
.ticket-detail__button:disabled {
  background-color: #aaa;
}
</style>
