<div class="list-card">
    <h2>List Student</h2>

    <div class="table-container">
    <?php 
    if(!empty($displayMessage))
    { ?>
        <div class="alert-success">
            <?php echo $displayMessage; ?>
        </div>
   <?php }
    
    ?>
    <table class="student-table" id="tbl-student-table">
        <thead>
            <th>ID</th>
            <th>Name</th>
            <th>Profile Image</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Phone No</th>
            <th>Bio</th>
            <th>Action</th>
        </thead>

        <tbody>
           
            
        </tbody>
    </table>
    </div>
</div>