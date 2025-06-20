<template>
  <div class="ticket-detail" v-if="ticket">
    <button class="btn btn--back" @click="$router.push('/tickets')">
      <i class="pi pi-arrow-left"></i> Back to Tickets
    </button>
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
        <p><strong>Confidence:</strong> <span class="badge badge--confidence">{{ ticket.confidence ? ticket.confidence.toFixed(2) : '-' }}</span></p>
      </div>

      <!-- Run Classification Button -->
      <button @click="runClassification" :disabled="loading" class="btn btn--primary">
        <i v-if="loading" class="pi pi-spin pi-spinner"></i>
        <span v-else><i class="pi pi-check"></i> Run Classification</span>
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
  min-height: 300px;
  background: var(--color-white);
  border-radius: 1.5rem;
  box-shadow: 0 4px 24px var(--color-shadow);
  padding: 2rem 2rem;
  margin: 2rem;
  box-sizing: border-box;
}
.ticket-detail__heading {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  color: var(--color-primary);
}
.ticket-detail__body {
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
  color: var(--color-secondary);
}
.ticket-detail__form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.ticket-detail__label {
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: 0.3rem;
}
.ticket-detail__select,
.ticket-detail__textarea {
  padding: 0.7rem 1rem;
  font-size: 1rem;
  border-radius: 8px;
  border: 1.5px solid var(--color-secondary);
  background: var(--color-accent);
  color: var(--color-primary);
  outline: none;
  transition: border 0.2s;
}
.ticket-detail__select:focus,
.ticket-detail__textarea:focus {
  border: 1.5px solid var(--color-primary);
}
.ticket-detail__textarea {
  min-height: 100px;
}
.ticket-detail__readonly {
  background: var(--color-accent);
  padding: 0.7rem 1rem;
  border-left: 4px solid var(--color-primary);
  border-radius: 0.5rem;
  color: var(--color-primary);
}
.badge {
  display: inline-block;
  padding: 0.25em 0.7em;
  border-radius: 1em;
  font-size: 0.95em;
  font-weight: 700;
  box-shadow: 0 1px 4px var(--color-shadow);
  margin-right: 0.2em;
}
.badge--confidence {
  background: var(--color-secondary);
  color: var(--color-white);
}
.btn {
  display: inline-block;
  padding: 0.55rem 1.3rem;
  border-radius: 8px;
  font-weight: 700;
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
.btn i {
  margin-right: 0.4em;
  font-size: 1.1em;
  vertical-align: middle;
}
.btn--back {
  background: var(--color-accent);
  color: var(--color-primary);
  border: 1.5px solid var(--color-secondary);
  margin-bottom: 1.2rem;
  font-weight: 700;
  font-size: 1rem;
  border-radius: 0.7rem;
  padding: 0.5rem 1.2rem;
  transition: background 0.2s, color 0.2s;
  box-shadow: 0 2px 8px var(--color-shadow);
  display: inline-flex;
  align-items: center;
  cursor: pointer;
}
.btn--back:hover {
  background: var(--color-secondary);
  color: var(--color-white);
}
.btn--back i {
  margin-right: 0.5em;
  font-size: 1.1em;
  vertical-align: middle;
}
@media (max-width: 900px) {
  .ticket-detail {
    padding: 1.2rem 0.5rem;
    border-radius: 1rem;
  }
}
@media (max-width: 600px) {
  .ticket-detail {
    padding: 0.7rem 0.2rem;
    border-radius: 0.7rem;
  }
  .ticket-detail__heading {
    font-size: 1.3rem;
  }
}
</style>
