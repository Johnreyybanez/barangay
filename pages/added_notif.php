<?php if(isset($_SESSION['added'])): ?>
    
    <script>
        $(document).ready(function () {
            Swal.fire({
  icon: 'success',
  title: 'Official Added',
  text: 'The official has been successfully added.'
});
        });
    </script>
    <?php unset($_SESSION['added']); ?>
<?php endif; ?>
