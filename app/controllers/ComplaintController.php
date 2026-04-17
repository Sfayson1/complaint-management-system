<?php
require_once __DIR__ . '/../models/Complaint.php';

class ComplaintController {
    private $complaintModel;

    public function __construct($db) {
        $this->complaintModel = new Complaint($db);
    }

    public function submitComplaint($customerId, $productServiceId, $categoryId, $description) {
        return $this->complaintModel->create($customerId, $productServiceId, $categoryId, $description);
    }

    public function saveComplaintImage($complaintId, $filePath) {
    return $this->complaintModel->saveImage($complaintId, $filePath);
    }


    public function getCustomerComplaints($customerId) {
        return $this->complaintModel->getByCustomerId($customerId);
    }
}
