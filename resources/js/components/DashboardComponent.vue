<template>
  <div class="container">
    <h2>Welcome to the Dashboard</h2>
    <button @click="logout" class="btn btn-danger mb-3">Logout</button>

    <!-- Leads Table -->
    <div v-if="leads.length > 0">
      <h4>Leads List</h4>

      <!-- Create Lead Button -->
      <button @click="openCreateModal" class="btn btn-primary mb-3">
        Create Lead
      </button>

      <!-- Update and Assign Buttons (Visible when a lead is selected) -->
      <div class="d-flex justify-content-end mb-3">
        <button 
          v-if="selectedLead" 
          @click="openModal(selectedLead)" 
          class="btn btn-secondary me-2">
          Update Lead
        </button>
        <button 
          v-if="selectedLead" 
          @click="openAssignModal" 
          class="btn btn-success">
          Assign Counselor
        </button>
      </div>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>Address</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lead in leads" :key="lead.id" @click="selectLead(lead)">
            <td>{{ lead.name }}</td>
            <td>{{ lead.email }}</td>
            <td>{{ lead.phone }}</td>
            <td>{{ lead.city }}</td>
            <td>{{ lead.address }}</td>
            <td>{{ new Date(lead.created_at).toLocaleString() }}</td>
            <td>
              <button class="btn btn-info">Details</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Creating a Lead -->
    <div v-if="showCreateModal" class="modal fade show" style="display: block;" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createModalLabel">Create Lead</h5>
            <button type="button" class="btn-close" @click="closeCreateModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Create Lead Form -->
            <form @submit.prevent="createLead">
              <div class="mb-3">
                <label class="form-label">Name:</label>
                <input v-model="newLead.name" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Email:</label>
                <input v-model="newLead.email" type="email" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Phone:</label>
                <input v-model="newLead.phone" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">City:</label>
                <input v-model="newLead.city" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Address:</label>
                <input v-model="newLead.address" type="text" class="form-control" required />
              </div>

              <button type="submit" class="btn btn-primary">Create Lead</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Editing and Assigning Counselor -->
    <div v-if="showModal" class="modal fade show" style="display: block;" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Edit Lead</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Lead Form -->
            <form @submit.prevent="updateLead">
              <div class="mb-3">
                <label class="form-label">Name:</label>
                <input v-model="selectedLead.name" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Email:</label>
                <input v-model="selectedLead.email" type="email" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Phone:</label>
                <input v-model="selectedLead.phone" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">City:</label>
                <input v-model="selectedLead.city" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Address:</label>
                <input v-model="selectedLead.address" type="text" class="form-control" required />
              </div>

              <button type="submit" class="btn btn-secondary">Update Lead</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Assign Counselor Modal -->
    <div v-if="showAssignModal" class="modal fade show" style="display: block;" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="assignModalLabel">Assign Counselor</h5>
            <button type="button" class="btn-close" @click="closeAssignModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Assign Counselor:</label>
              <select v-model="selectedCounselor" class="form-select">
                <option value="" disabled>Select a Counselor</option>
                <option v-for="counselor in counselors" :key="counselor.id" :value="counselor.id">
                  {{ counselor.name }}
                </option>
              </select>
            </div>

            <button @click="assignCounselor" class="btn btn-success" :disabled="!selectedCounselor">
              Assign Counselor
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- No Leads Available -->
    <div v-else>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      base_url: 'http://127.0.0.1:8000', 
      leads: [],
      counselors: [],
      showModal: false,
      showAssignModal: false,
      showCreateModal: false,
      selectedLead: null,
      selectedCounselor: null,
      newLead: {
        name: '',
        email: '',
        phone: '',
        city: '',
        address: '',
      },
    };
  },
  methods: {
    // Logout function to clear the token and redirect to login page
    logout() {
      localStorage.removeItem('access_token');
      this.$router.push('/'); // Redirect to login page
    },

    // Fetch leads from the API
    async fetchLeads() {
      const base_url = 'http://127.0.0.1:8000'; // API base URL
      try {
        const response = await axios.get(`${base_url}/api/leads`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        this.leads = response.data.data; // Store the leads in the component's data
      } catch (error) {
        console.error('Error fetching leads:', error);
      }
    },

    // Fetch counselors from the API
    async fetchCounselors() {
      const base_url = 'http://127.0.0.1:8000'; // API base URL
      try {
        const response = await axios.get(`${base_url}/api/counselors`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        this.counselors = response.data; // Store the counselors in the component's data
      } catch (error) {
        console.error('Error fetching counselors:', error);
      }
    },

    // Create a new lead
    async createLead() {
      const base_url = 'http://127.0.0.1:8000'; // API base URL
      try {
        await axios.post(`${base_url}/api/leads/`, this.newLead, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        alert('Lead created successfully!');
        this.fetchLeads();
        this.closeCreateModal();
      } catch (error) {
        console.error('Error creating lead:', error);
      }
    },

    // Open modal to create a lead
    openCreateModal() {
      this.showCreateModal = true;
    },

    // Close modal for creating a lead
    closeCreateModal() {
      this.showCreateModal = false;
      this.newLead = { name: '', email: '', phone: '', city: '', address: '' }; // Clear the form
    },

    // Select a lead
    selectLead(lead) {
      this.selectedLead = lead;
    },

    // Open modal to edit lead
    openModal(lead) {
      this.selectedLead = { ...lead };
      this.showModal = true;
    },

    // Open modal to assign a counselor
    openAssignModal() {
      this.showAssignModal = true;
    },

    // Close modal for editing lead
    closeModal() {
      this.showModal = false;
    },

    // Close modal for assigning counselor
    closeAssignModal() {
      this.showAssignModal = false;
    },

    // Update lead details
    async updateLead() {
      const base_url = 'http://127.0.0.1:8000'; // API base URL
      try {
        await axios.post(`${base_url}/api/leads/${this.selectedLead.id}`, this.selectedLead, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        alert('Lead updated successfully!');
        this.fetchLeads();
        this.closeModal();
      } catch (error) {
        console.error('Error updating lead:', error);
      }
    },

    // Assign counselor to lead
    async assignCounselor() {
     try {
    // Send POST request with the correct URL and request body
    await axios.post(`${this.base_url}/api/assign-lead/`, {
      lead_id: this.selectedLead.id,    // Use selectedLead.id for lead_id
      counselor_id: this.selectedCounselor,  // Use selectedCounselor for counselor_id
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`,  // Use the token for authorization
      },
    });

    // Show success message
    alert('Counselor assigned successfully!');

    // Refresh the leads list
    this.fetchLeads();

    // Close the modal
    this.closeAssignModal();
  } catch (error) {
    // Handle any error
    console.error('Error assigning counselor:', error);
  }
}

  },
  mounted() {
    this.fetchLeads();
    this.fetchCounselors();
  },
};
</script>

<style scoped>
.modal-content {
  width: 500px;
}

.modal-header {
  background-color: #f8f9fa;
}

button {
  margin-right: 10px;
}
</style>
