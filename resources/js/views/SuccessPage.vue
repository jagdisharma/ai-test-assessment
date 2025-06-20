<template>
  <div class="success-page">
    <div class="success-page__box">
      <h1 class="success-page__title">🎉 Ticket Submitted Successfully!</h1>
      <p class="success-page__message">
        Thank you for reaching out. Our support team will contact you soon.
      </p>
      <router-link to="/" class="success-page__link">← Back to Home</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SuccessPage',
}
</script>

<style scoped>
.success-page {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4rem 1rem;
}

.success-page__box {
  max-width: 600px;
  background-color: #ffffff;
  padding: 3rem 2rem;
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  text-align: center;
}

.success-page__title {
  font-size: 2rem;
  color: #22c55e;
  margin-bottom: 1rem;
}

.success-page__message {
  font-size: 1.1rem;
  color: #444;
  margin-bottom: 2rem;
}

.success-page__link {
  text-decoration: none;
  color: #2563eb;
  font-weight: 600;
}
</style>
