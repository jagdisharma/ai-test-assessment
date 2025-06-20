<template>
  <div class="success-page">
    <div class="success-page__box">
      <h1 class="success-page__title"><i class="pi pi-check-circle"></i> Ticket Submitted Successfully!</h1>
      <p class="success-page__message">
        Thank you for reaching out. Our support team will contact you soon.
      </p>
      <router-link to="/tickets" class="success-page__link"><i class="pi pi-home"></i> Back to Tickets</router-link>
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
  background: linear-gradient(135deg, #FAEAB1 0%, #E5BA73 100%);
  padding: 3rem 2rem;
  border-radius: 1.5rem;
  box-shadow: 0 10px 30px rgba(197,137,64,0.10);
  text-align: center;
}

.success-page__title {
  font-size: 2rem;
  color: #16a34a;
  margin-bottom: 1rem;
  font-weight: 800;
}

.success-page__title i {
  margin-right: 0.5rem;
  font-size: 1.3em;
  vertical-align: middle;
}

.success-page__message {
  font-size: 1.1rem;
  color: #C58940;
  margin-bottom: 2rem;
}

.success-page__link {
  text-decoration: none;
  color: #fff;
  background: #C58940;
  font-weight: 700;
  padding: 0.7rem 1.5rem;
  border-radius: 0.7rem;
  transition: background 0.2s, color 0.2s;
  box-shadow: 0 2px 8px rgba(197,137,64,0.08);
  display: inline-block;
}

.success-page__link:hover {
  background: #E5BA73;
  color: #fff;
}

.success-page__link i {
  margin-right: 0.4em;
  font-size: 1.1em;
  vertical-align: middle;
}

@media (max-width: 600px) {
  .success-page__box {
    padding: 1.5rem 0.5rem;
    border-radius: 0.7rem;
  }
  .success-page__title {
    font-size: 1.3rem;
  }
}
</style>
