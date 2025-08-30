<?php if(isset($_SESSION['delete'])): ?>
   
    <script>
        $(document).ready(function () {
            Swal.fire({
  icon: 'success',
  title: 'Deleted',
  text: 'Selected have been deleted.'
});
        });
    </script>
    <?php unset($_SESSION['delete']); ?>
<?php endif; ?>
