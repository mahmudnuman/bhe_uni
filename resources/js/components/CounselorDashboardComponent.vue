<template>
  <div class="container">
    <h2>Welcome to the Dashboard</h2>
    <button @click="logout" class="btn btn-danger mb-3">Logout</button>

    <!-- Leads Table -->
    <div v-if="leads.length > 0">
      <h4>Leads List</h4>

      <div class="d-flex justify-content-end mb-3">
        <button v-if="selectedLead" @click="openStatusModal" class="btn btn-secondary me-2">
          Update Status
        </button>
        <button v-if="selectedLead" @click="openMoveToApplicationModal" class="btn btn-success">
          Move to Application
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
          <tr v-for="lead in leads" :key="lead.id" 
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

    <!-- Update Status Modal -->
    <div v-if="showStatusModal" class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Status</h5>
            <button type="button" class="close" @click="closeStatusModal">&times;</button>
          </div>
          <div class="modal-body">
            <div v-if="isLeadSelected">
              <label for="lead-status">Select Lead Status:</label>
              <select v-model="selectedLeadStatus" class="form-control">
                <option v-for="status in leadStatuses" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
            </div>
            <div v-else>
              <label for="application-status">Select Application Status:</label>
              <select v-model="selectedApplicationStatus" class="form-control">
                <option v-for="status in applicationStatuses" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeStatusModal">Close</button>
            <button type="button" class="btn btn-primary" @click="updateStatus">Update Status</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showStatusModal" class="modal-backdrop fade show"></div>

    <!-- Move to Application Modal -->
    <div v-if="showMoveToApplicationModal" class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Move to Application</h5>
            <button type="button" class="close" @click="closeMoveToApplicationModal">&times;</button>
          </div>
          <div class="modal-body">
            <label for="application-status">Select Application Status:</label>
            <select v-model="selectedMoveToApplicationStatus" class="form-control">
              <option v-for="status in applicationStatuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeMoveToApplicationModal">Close</button>
            <button type="button" class="btn btn-primary" @click="moveLeadToApplication">Move Lead</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showMoveToApplicationModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      base_url: 'http://127.0.0.1:8000', 
      leads: [],
      selectedLead: null,
      selectedLeadStatus: '',  // Status for leads
      selectedApplicationStatus: '',  // Status for applications
      selectedMoveToApplicationStatus: '',  // Status for when moving lead to application
      showStatusModal: false,
      showMoveToApplicationModal: false,
      leadStatuses: ['In Progress', 'Bad Timing', 'Not Interested', 'Not Qualified'],
      applicationStatuses: ['In Progress', 'Approved', 'Rejected'],  // Correct application statuses
    };
  },
  computed: {
    isLeadSelected() {
      return this.selectedLead !== null;
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('access_token');
      this.$router.push('/');
    },

    async fetchLeads() {
      try {
        const response = await axios.get(`${this.base_url}/api/leads`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        });
        this.leads = response.data.data;
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Failed to fetch leads.',
          timer: 2000,
          showConfirmButton: false,
        });
        console.error('Error fetching leads:', error);
      }
    },

    selectLead(lead) {
      this.selectedLead = lead;
      this.selectedLeadStatus = lead.status;
    },

    openStatusModal() {
      if (this.selectedLead) {
        this.showStatusModal = true;
      }
    },

    closeStatusModal() {
      this.showStatusModal = false;
    },

    openMoveToApplicationModal() {
      if (this.selectedLead) {
        this.showMoveToApplicationModal = true;
      }
    },

    closeMoveToApplicationModal() {
      this.showMoveToApplicationModal = false;
    },

    async updateStatus() {
      if (!this.selectedLead) {
        Swal.fire({
          icon: 'warning',
          title: 'No Lead Selected!',
          text: 'Please select a lead first.',
          timer: 2000,
          showConfirmButton: false,
        });
        return;
      }

      try {
        // If lead selected, update lead status
        if (this.isLeadSelected) {
          await axios.post(`${this.base_url}/api/assignments/status`, {
            status: this.selectedLeadStatus,
            lead_id: this.selectedLead.id,
          }, {
            headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
          });
        }
        // Else, update application status
        else {
          await axios.post(`${this.base_url}/api/assignments/application-status`, {
            status: this.selectedApplicationStatus,
            lead_id: this.selectedLead.id,
          }, {
            headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
          });
        }

        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Status updated successfully!',
          timer: 2000,
          showConfirmButton: false,
        });

        this.showStatusModal = false;
        this.fetchLeads();  // Refresh lead data after status update
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Update Failed!',
          text: 'Something went wrong while updating the status.',
          timer: 2000,
          showConfirmButton: false,
        });
        console.error('Error updating status:', error);
      }
    },

    async moveLeadToApplication() {
      if (!this.selectedLead) {
        Swal.fire({
          icon: 'warning',
          title: 'No Lead Selected!',
          text: 'Please select a lead first.',
          timer: 2000,
          showConfirmButton: false,
        });
        return;
      }

      try {
        await axios.post(`${this.base_url}/api/move-to-application`, {
          lead_id: this.selectedLead.id,
          status: this.selectedMoveToApplicationStatus,
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        });

        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Lead moved to application successfully!',
          timer: 2000,
          showConfirmButton: false,
        });

        this.showMoveToApplicationModal = false;
        this.fetchLeads();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Move Failed!',
          text: 'Something went wrong while moving the lead.',
          timer: 2000,
          showConfirmButton: false,
        });
        console.error('Error moving lead:', error);
      }
    }
  },
  mounted() {
    this.fetchLeads();
  }
};
</script>

<style>
.selected-row {
  background-color: #d0ebff !important;
  font-weight: bold;
}
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
