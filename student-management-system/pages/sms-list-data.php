<table>
    <thead>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Gender</th>
    </thead>
    <tbody>
        <?php if(count($students) > 0) 
        {
            foreach($students as $student)
            {?>
                <tr>
                <td><?php echo $student['id'];?></td>
                <td><?php echo $student['name'];?></td>
                <td><?php echo $student['email'];?></td>
                <td><?php echo $student['phno'];?></td>
                <td><?php echo $student['gender'];?></td>
                </tr>
           <?php }
        } else {
            echo "No Data Found";
        }
        ?>
    </tbody>
</table>  