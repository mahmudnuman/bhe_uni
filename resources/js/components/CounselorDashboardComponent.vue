<template>
  <div class="container">
    <h2>Welcome to the Dashboard</h2>
    <router-link to="/kanban-board">Go to Kanban Board</router-link>

    <button @click="logout" class="btn btn-danger mb-3">Logout</button>

    <!-- Leads Table -->
    <div v-if="leads.length > 0">
      <h4>Leads List</h4>

      <div class="d-flex justify-content-end mb-3">
        <button v-if="selectedLead" @click="openStatusModal" class="btn btn-secondary me-2">
          Update Lead Status
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

    <!-- Update Lead Status Modal -->
    <div v-if="showStatusModal" class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Lead Status</h5>
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
            <h5 class="modal-title">Move Lead to Application</h5>
            <button type="button" class="close" @click="closeMoveToApplicationModal">&times;</button>
          </div>
          <div class="modal-body">
            <label for="move-status">Select Application Status:</label>
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

    <!-- Application Status Section -->
    <div v-if="applications.length > 0" class="mt-5">
      <h4>Applications List</h4>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="application in applications" :key="application.id" 
              @click="selectApplication(application)" 
              :class="{ 'selected-row': selectedApplication && selectedApplication.id === application.id }">
            <td>{{ application.lead.name }}</td>
            <td>{{ application.lead.email }}</td>
            <td>{{ application.status }}</td>
            <td>
              <button class="btn btn-info" @click="openApplicationStatusModal(application)">
                Update Status
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Update Application Status Modal -->
    <div v-if="showApplicationStatusModal" class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Application Status</h5>
            <button type="button" class="close" @click="closeApplicationStatusModal">&times;</button>
          </div>
          <div class="modal-body">
            <label for="application-status">Select Status:</label>
            <select v-model="selectedApplicationStatus" class="form-control">
              <option v-for="status in applicationStatuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeApplicationStatusModal">Close</button>
            <button type="button" class="btn btn-primary" @click="updateApplicationStatus">Update Status</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showApplicationStatusModal" class="modal-backdrop fade show"></div>

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
      applications: [],
      selectedLead: null,
      selectedApplication: null,
      selectedLeadStatus: '',
      selectedApplicationStatus: '',
      selectedMoveToApplicationStatus: '',
      showStatusModal: false,
      showMoveToApplicationModal: false,
      showApplicationStatusModal: false,
      leadStatuses: ['In Progress', 'Bad Timing', 'Not Interested', 'Not Qualified'],
      applicationStatuses: ['In Progress', 'Approved', 'Rejected'],
    };
  },
  computed: {
    isLeadSelected() {
      return this.selectedLead !== null;
    },
    isApplicationSelected() {
      return this.selectedApplication !== null;
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('access_token');
      this.$router.push('/');
    },
    goToKanbanBoard() {
      this.$router.push('/kanban-board'); // Redirect to Kanban Board
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

    async fetchApplications() {
      try {
        const response = await axios.get(`${this.base_url}/api/applications`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        });
        this.applications = response.data;
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Failed to fetch applications.',
          timer: 2000,
          showConfirmButton: false,
        });
        console.error('Error fetching applications:', error);
      }
    },

    selectLead(lead) {
      this.selectedLead = lead;
      this.selectedLeadStatus = lead.status;
    },

    selectApplication(application) {
      this.selectedApplication = application;
      this.selectedApplicationStatus = application.status;
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

    openApplicationStatusModal(application) {
      this.selectedApplicationStatus = application.status;
      this.showApplicationStatusModal = true;
    },

    closeApplicationStatusModal() {
      this.showApplicationStatusModal = false;
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
        await axios.post(`${this.base_url}/api/assignments/status`, {
          status: this.selectedLeadStatus,
          lead_id: this.selectedLead.id,
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        });

        Swal.fire({
          icon: 'success',
          title: 'Status Updated!',
          text: 'Lead status updated successfully.',
          timer: 2000,
          showConfirmButton: false,
        });

        this.fetchLeads();
        this.closeStatusModal();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Failed to update status.',
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
        await axios.post(`${this.base_url}/api/applications`, {
          lead_id: this.selectedLead.id,
          status: this.selectedMoveToApplicationStatus,
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        });

        Swal.fire({
          icon: 'success',
          title: 'Lead Moved!',
          text: 'Lead has been successfully moved to applications.',
          timer: 2000,
          showConfirmButton: false,
        });

        this.fetchLeads();
        this.fetchApplications();
        this.closeMoveToApplicationModal();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Failed to move lead.',
          timer: 2000,
          showConfirmButton: false,
        });
        console.error('Error moving lead to application:', error);
      }
    },

    async updateApplicationStatus() {
      if (!this.selectedApplication) {
        Swal.fire({
          icon: 'warning',
          title: 'No Application Selected!',
          text: 'Please select an application first.',
          timer: 2000,
          showConfirmButton: false,
        });
        return;
      }

      try {
        const applicationId = this.selectedApplication.id;
        await axios.post(`${this.base_url}/api/applications/${applicationId}`, {
          status: this.selectedApplicationStatus,
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
        });

        Swal.fire({
          icon: 'success',
          title: 'Application Status Updated!',
          text: 'Application status updated successfully.',
          timer: 2000,
          showConfirmButton: false,
        });

        this.fetchApplications();
        this.closeApplicationStatusModal();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Failed to update application status.',
          timer: 2000,
          showConfirmButton: false,
        });
        console.error('Error updating application status:', error);
      }
    },
  },
  mounted() {
    this.fetchLeads();
    this.fetchApplications();
  }
};
</script>

<style scoped>
.selected-row {
  background-color: #d1e7dd;
}
</style>
