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
          <tr 
            v-for="lead in leads" 
            :key="lead.id" 
            @click="selectLead(lead)"
            :class="{ 'selected-row': selectedLead && selectedLead.id === lead.id }">
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
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';  // Import SweetAlert2

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
    logout() {
      localStorage.removeItem('access_token');
      this.$router.push('/'); // Redirect to login page
    },

    async fetchLeads() {
      try {
        const response = await axios.get(`${this.base_url}/api/leads`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        this.leads = response.data.data;
      } catch (error) {
        console.error('Error fetching leads:', error);
      }
    },

    async fetchCounselors() {
      try {
        const response = await axios.get(`${this.base_url}/api/counselors`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        this.counselors = response.data;
      } catch (error) {
        console.error('Error fetching counselors:', error);
      }
    },

    async createLead() {
      try {
        await axios.post(`${this.base_url}/api/leads/`, this.newLead, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        Swal.fire('Success!', 'Lead created successfully!', 'success');
        this.fetchLeads();
        this.closeCreateModal();
      } catch (error) {
        console.error('Error creating lead:', error);
      }
    },

    openCreateModal() {
      this.showCreateModal = true;
    },

    closeCreateModal() {
      this.showCreateModal = false;
      this.newLead = { name: '', email: '', phone: '', city: '', address: '' };
    },

    selectLead(lead) {
      this.selectedLead = lead;
    },

    openModal(lead) {
      this.selectedLead = { ...lead };
      this.showModal = true;
    },

    openAssignModal() {
      this.showAssignModal = true;
    },

    closeModal() {
      this.showModal = false;
    },

    closeAssignModal() {
      this.showAssignModal = false;
    },

    async updateLead() {
      try {
        await axios.post(`${this.base_url}/api/leads/${this.selectedLead.id}`, this.selectedLead, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        Swal.fire('Success!', 'Lead updated successfully!', 'success');
        this.fetchLeads();
        this.closeModal();
      } catch (error) {
        console.error('Error updating lead:', error);
      }
    },

    async assignCounselor() {
      try {
        await axios.post(`${this.base_url}/api/assign-lead/`, {
          lead_id: this.selectedLead.id,
          counselor_id: this.selectedCounselor,
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        });

        Swal.fire('Success!', 'Counselor assigned successfully!', 'success');
        this.fetchLeads();
        this.closeAssignModal();
      } catch (error) {
        console.error('Error assigning counselor:', error);
      }
    },
  },
  mounted() {
    this.fetchLeads();
    this.fetchCounselors();
  },
};
</script>

<style scoped>
/* Your existing styles here */
.selected-row {
  background-color: #d1ecf1;
}
</style>
