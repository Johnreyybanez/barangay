<?php if(isset($_SESSION['edited'])): ?>
    <script>
        $(document).ready(function () {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Edit Successfully Saved!',
                showConfirmButton: true,
              
            });
        });
    </script>
    <?php unset($_SESSION['edited']); ?>
<?php endif; ?>
