{include file="~blox/data.table.tpl"}

<section class="widget"> 
    <table id="shop-items" class="table table-striped dataTable no-footer"  width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>                
                <th>SKU</th> 
                <th>Tags</th>
                <th>In Stock</th>
                <th>Viewed</th>
            </tr>
        </thead> 
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>                
                <th>SKU</th> 
                <th>Tags</th>
                <th>In Stock</th>
            </tr>
        </tfoot>
    </table>
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#shop-items').dataTable( {
                // "processing": true,
                // "serverSide": true,
                // "autoload" : true,
                "sDom": "<'row table-top-control'<'col-md-6 hidden-xs per-page-selector'l><'col-md-6'f>r>t<'row table-bottom-control'<'col-md-6'i><'col-md-6'p>>",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ &nbsp; records per page"
                },
                "ajax": "/{$toBackDoor}/{$Xtra}/{$method}/.json"
            }); 
        }); 
    </script>
</section>
