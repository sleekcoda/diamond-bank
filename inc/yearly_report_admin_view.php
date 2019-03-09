<style>
    #wpcontent {
        background-color: #fff;
    }
    table{
        text-align:left;
    }
    #yearly-report-form input[type="text"] {
        width: 250px;
        border-radius: 5px;
        box-shadow: none;
    }#report-table{
        height:400px;
        overflow-y:scroll;
        overflow-x:hidden;
        font-size:1.2em;
    }
    #report-table tr:nth-of-type(even){
        background-color:#efefef;
        
    }
    table#report_list, h3#title {
        width: 95%;
        margin: 1em auto;
    }
    table#report_list tbody tr:nth-of-type(even) {
        background-color: rgba(0,0,0,.2);
    }
    table#report_list tbody tr:hover{
        background-color: rgba(0,0,0,.35);
    }
    table#report_list tbody td {
        padding: 3px;
    }
    div#record-container {
        height: 500px;
        overflow: scroll;
    }
</style>
<div class="wrap">
    <h2>Yearly Report Updates</h2>
    <h3>Add new record</h3>
    <form id="yearly-report-form">
    <?php wp_nonce_field('yearly_form_action','nonce'); ?>
        <table>
            <tr>
                <td>
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="quarterly">Quarter</option>
                    <option value="abridge">Abridge</option>
                    <option value="annual">Annual</option>
                </select></td>
                <td><input placeholder="Enter document title" type="text" id="title" name="title"/></td>
                <td><input type="date" name="date" id="date" /></td>
                <td>
                    <input id="document_id" type="hidden" name="document_id"/>
                    <button data-id="document_id" id="upload" type="button" class="button button-primary">Upload document</button>
                    <span data-filename="document_id" id="notification-box"></span>
                </td>

            </tr>
            
            <tr><td><button class="button button-primary" type="submit">Save record</button></td></tr>
        </table>
    </form>  
        <div id="response"></div>

<div>
<h3 id="title">Reports</h3>
<div id="record-container">
<table  id="report_list" style="font-size:1.2em;">
    <thead>
    <th>S/N</th>
    <th>Year</th>
    <th>Document Title</th>
    <th>Category</th>
    <th>Date</th>
        
    </thead><tbody>
    
    <?php $i=0; foreach($reports as $key => $value): ?>
    
        <tr >
        <td><?php echo ++$i; ?></td>
        <td><?php echo $value['year']; ?></td>
        <td><a href="<?php echo wp_get_attachment_url($value['document_id']); ?>" target="_blank"><?php echo $value['title']; ?></a></td>
        <td><?php echo $value['category']; ?></td>
        <td><?php echo $value['date']; ?></td>
        <td><a class="delete_record" data-id="<?php echo $value['id']; ?>" href="#">Delete</a></td>
        </tr>
        
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>