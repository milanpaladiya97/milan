<?php
ob_start();
class Results_List_Table extends WP_List_Table{
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Result', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Results', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'results_notices' ) );
        add_filter( 'hidden_columns', array( $this, 'get_hidden_columns'), 10, 2 );

    }

    /**
     * Override of table nav to avoid breaking with bulk actions & according nonce field
     */
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
            
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }

    public function extra_tablenav( $which ){
        global $wpdb;
        $titles_sql = "SELECT {$wpdb->prefix}aysquiz_quizes.title,
                              {$wpdb->prefix}aysquiz_quizes.id 
                       FROM {$wpdb->prefix}aysquiz_quizes";
        $quiz_titles = $wpdb->get_results($titles_sql);
        
        $users_sql = "SELECT {$wpdb->prefix}aysquiz_reports.user_id
                      FROM {$wpdb->prefix}aysquiz_reports
                      GROUP BY user_id";
        $users = $wpdb->get_results($users_sql);
        $quiz_id = null;
        $user_id = null;
        if( isset( $_GET['filterby'] )){
            $quiz_id = intval($_GET['filterby']);
        }
        if( isset( $_GET['wpuser'] )){
            $user_id = intval($_GET['wpuser']);
        }
        ?>
        <div id="quiz-filter-div" class="alignleft actions bulkactions">
            <select name="filterby" id="bulk-action-selector-top">
                <option value=""><?php echo __('Select Quiz',$this->plugin_name)?></option>
                <?php
                    foreach($quiz_titles as $key => $q_title){
                        $selected = "";
                        if($quiz_id === intval($q_title->id)){
                            $selected = "selected";
                        }
                        echo "<option ".$selected." value='".$q_title->id."'>".$q_title->title."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction" class="cat-filter-apply button" value="Filter">
        </div>
        <div id="user-filter-div" class="alignleft actions bulkactions">
            <select name="filterbyuser" id="bulk-action-selector-top2">
                <option value=""><?php echo __('Select User',$this->plugin_name)?></option>
                <?php
                    foreach($users as $key => $user){
                        $selected = "";
                        if($user_id === intval($user->user_id)){
                            $selected = "selected";
                        }
                        if(intval($user->user_id) == 0){
                            $name = __( 'Guest', $this->plugin_name );
                        }else{
                            $wpuser = get_userdata( intval($user->user_id) );
                            $name = $wpuser->data->display_name;
                        }
                        echo "<option ".$selected." value='".$user->user_id."'>".$name."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction2" class="user-filter-apply button" value="Filter">
        </div>

         <div id="score-filter-div" class="alignleft actions bulkactions">
            <select name="score_filter" id="bulk-action-selector-top3">
                <option value=""><?php echo __('Select Score',$this->plugin_name)?></option>

                <?php
                $selected_poor = '';
                $selected_average = '';
                $selected_good = '';
                $selected_excellent = '';
                if( isset( $_REQUEST['score_filter'] ) ){
         // echo "ok"; die();
            $score_filter = $_REQUEST['score_filter'];
            if($score_filter == 'poor'){
               $selected_poor = 'selected';
            }
            else if($score_filter == 'average'){
                $selected_average = 'selected';
            }
            else if($score_filter == 'good'){
              $selected_good = 'selected';
            }
            else if($score_filter == 'excellent'){
                $selected_excellent = 'selected';
            }
        }


                 ?>
                <option <?php echo $selected_poor; ?> value="poor">Poor</option>
                <option <?php echo $selected_average; ?> value="average">Average</option>
                <option <?php echo $selected_good; ?> value="good">Good</option>
                <option <?php echo $selected_excellent; ?> value="excellent">Excellent</option>

            </select>
            <input type="button" id="doaction3" class="score-filter-apply button" value="Filter">
        </div>

        <a style="margin: 3px 8px 0 0;display:inline-block;" href="?page=<?php echo $_REQUEST['page'] ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        <?php
    }
    
    protected function get_views() {
        $published_count = $this->readed_records_count();
        $unpublished_count = $this->unread_records_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";
        if(isset($_GET['fstatus'])){
            switch($_GET['fstatus']){
                case "0":
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case "1":
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                default:
                    $selected_all = " style='font-weight:bold;' ";
                    break;
            }
        }else{
            $selected_all = " style='font-weight:bold;' ";
        }
        
        $status_links = array(
            "all" => "<a ".$selected_all." href='?page=".esc_attr( $_REQUEST['page'] )."'>". __( 'All', $this->plugin_name )." (".$all_count.")</a>",
            "readed" => "<a ".$selected_1." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=1'>". __( 'Readed', $this->plugin_name )." (".$published_count.")</a>",
            "unreaded"   => "<a ".$selected_0." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=0'>". __( 'Unreaded', $this->plugin_name )." (".$unpublished_count.")</a>"
        );
        return $status_links;
    }    

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_reports( $per_page = 50, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports";

        $sql .= self::get_where_condition();

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        }
        else{
            $sql .= ' ORDER BY end_date DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public static function get_where_condition(){
        $where = array();
        $sql = '';

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $s = array();
            $s[] = ' `user_name` LIKE \'%'.$search.'%\' ';
            $s[] = ' `user_email` LIKE \'%'.$search.'%\' ';
            $s[] = ' `user_phone` LIKE \'%'.$search.'%\' ';
            $s[] = ' `score` LIKE \'%'.$search.'%\' ';
            $where[] = ' ( ' . implode(' OR ', $s) . ' ) ';
        }

        if(isset( $_REQUEST['fstatus'] )){            
            $fstatus = intval($_REQUEST['fstatus']);
            switch($fstatus){
                case 0:
                    $where[] = ' `read` = 0 ';
                    break;
                case 1:                    
                    $where[] = ' `read` = 1 ';
                    break;
            }
        }

        if(! empty( $_REQUEST['filterby'] ) && $_REQUEST['filterby'] > 0){
            $cat_id = intval($_REQUEST['filterby']);
            $where[] = ' `quiz_id` = '.$cat_id.' ';
        }

        if( isset( $_REQUEST['wpuser'] ) ){
            $user_id = intval($_REQUEST['wpuser']);
            $where[] = ' `user_id` = '.$user_id.' ';
        }
        if( isset( $_REQUEST['score_filter'] ) ){
         // echo "ok"; die();
            $score_filter = $_REQUEST['score_filter'];
            if($score_filter == 'poor'){
                $where[] = ' `score`  IN(0,5,10,15,20,25,30,35,40)';
            }
            else if($score_filter == 'average'){
                $where[] = ' `score` IN(40,45,50,55,60,65,70)';
            }
            else if($score_filter == 'good'){
                $where[] = ' `score` IN(70,75,80,85,90)';
            }
            else if($score_filter == 'excellent'){
                $where[] = ' `score` IN(90,95,100)';
            }
        }
        
        if( ! empty($where) ){
            $sql = " WHERE " . implode( " AND ", $where );
        }

        // echo $sql; die();
        return $sql;
    }    

    public function get_report_by_id( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }


    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_reports( $id ) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_reports",
            array( 'id' => $id ),
            array( '%d' )
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports";
        $sql .= self::get_where_condition();
        return $wpdb->get_var( $sql );
    }
    
    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports";

        return $wpdb->get_var( $sql );
    }
    
    public static function unread_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0;";

        return $wpdb->get_var( $sql );
    }
    
    public function readed_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 1;";

        return $wpdb->get_var( $sql );
    }


    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no results yet.', $this->plugin_name );
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'quiz_id':
            case 'user_id':
            case 'user_ip':
            case 'user_name':
            case 'user_email':
            case 'user_phone':
            case 'start_date':
            case 'end_date':
            case 'duration':
            case 'id':
                return $item[ $column_name ];
                break;
            case 'score':
             $score_text = '';
                if($item[ $column_name ] >= '0' && $item[ $column_name ] <= '40'){
                    $score_text = 'Poor';
                }else if($item[ $column_name ] > '40' && $item[ $column_name ] <= '70'){
                    $score_text = 'Average';
                }
                else if($item[ $column_name ] > '70' && $item[ $column_name ] <= '90'){
                    $score_text = 'Good';
                }
                else if($item[ $column_name ] >= '90'){
                    $score_text = 'Excellent';
                }

                return $item[ $column_name ] . "/100" . '</br>' .$score_text;
                break;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" class="ays_result_delete" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_quiz_id( $item ) {
        global $wpdb;
        
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );
        
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id={$item['quiz_id']}", "ARRAY_A");
        if($item['read'] == 0){
            $result_read = "style='font-weight:bold;'";
        }else{
            $result_read = "";
        }
        $restitle = Quiz_Maker_Admin::ays_restriction_string("word",stripcslashes($result['title']), 5);
        if($result == null){
            $title = __( 'Quiz has been deleted', $this->plugin_name );
        }else{
            // $title = sprintf( '<a href="javascript:void(0)" data-result="%d" class="%s" '.$result_read.'>%s</a>', absint( $item['id'] ), 'ays-show-results', $restitle);
            $title = sprintf( '<a href="javascript:void(0)" data-result="%d" class="%s" '.$result_read.'>%s</a><input type="hidden" value="%d" class="ays_result_read">', absint( $item['id'] ), 'ays-show-results', $restitle,  $item['read']);
        }
        // $title = sprintf( '<a href="javascript:void(0)" data-result="%d" class="%s">%s</a><input type="hidden" value="%d" class="ays_result_read">', absint( $item['id'] ), 'ays-show-results', $name, $item['read']);
        $quiz_id =  isset($result['quiz_id']) ? $result['quiz_id'] : 0;
        $actions = array(
            'view-details' => sprintf( '<a href="javascript:void(0);" data-result="%d" class="%s">%s</a>', absint( $item['id'] ), 'ays-show-results', 'View details'),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="this report" href="?page=%s&action=%s&result=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );
        
        // $actions = array(
        //     'delete' => sprintf( '<a href="?page=%s&action=%s&result=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        // );

        return $title . $this->row_actions( $actions );
    }

    function column_user_id( $item ) {
        $user_id = intval($item['user_id']);
        if($user_id == 0){
            $name = "Guest";
        }else{
            $user = get_userdata($user_id);
            $name = $user->data->display_name;
        }
        return $name;
    }
    
    function column_quiz_rate( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $options = json_decode($item['options'], true);
        $rate_id = (isset($options['rate_id'])) ? $options['rate_id'] : null;
        if($rate_id !== null){
            $margin_of_icon = "style='margin-left: 5px;'";
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$rate_id}", "ARRAY_A");
            if($this->isJSON($result['options'])){
                $review_json = json_decode($result['options'], true);
                $review = $review_json['reason'];
            }elseif($result['options'] != ''){
                $review = $result['options'];
            }else{
                $review = $result['review'];
            }
            $reason = htmlentities(stripslashes(wpautop($review)));
            if($reason == ''){
                $reason = __("No review provided", $this->plugin_name);
            }
            $score = $result['score'];
            $title = "<span data-result='".absint( $item['id'] )."' class='ays-show-rate-avg'>
                        $score
                        <a class='ays_help' $margin_of_icon data-template='<div class=\"rate_tooltip tooltip\" role=\"tooltip\"><div class=\"arrow\"></div><div class=\"rate-tooltip-inner tooltip-inner\"></div></div>' data-toggle='tooltip' data-html='true' title='$reason'><i class='ays_fa ays_fa_info_circle'></i></a>                        
                </span>";
        }else{
            $margin_of_icon = '';
            $reason = __("No rate provided", $this->plugin_name);
            $score = '';
            $title = "";
        }
        return $title;
    }
    
    function column_duration( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $options = json_decode($item['options'], true);
        $passed_time = (isset($options['passed_time'])) ? $options['passed_time'] : null;
        if($passed_time !== null){
            $title = $passed_time;
        }else{
            $title = __('No data', $this->plugin_name);
        }
        return $title;
    }

    function isJSON($string){
       return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    
    function ays_get_average_of_rates($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                    => '<input type="checkbox" />',
            'quiz_id'               => __( 'Quiz', $this->plugin_name ),
            'user_id'               => __( 'WP User', $this->plugin_name ),
            'user_ip'               => __( 'User IP', $this->plugin_name ),
            'user_name'             => __( 'Name', $this->plugin_name ),
            'user_email'            => __( 'Email', $this->plugin_name ),
            'user_phone'            => __( 'Phone', $this->plugin_name ),
            'quiz_rate'             => __( 'Rate', $this->plugin_name ),
            'start_date'            => __( 'Start', $this->plugin_name ),
            'end_date'              => __( 'End', $this->plugin_name ),
            'duration'              => __( 'Duration', $this->plugin_name ),
            'score'                 => __( 'Score', $this->plugin_name ),
            'id'                    => __( 'ID', $this->plugin_name ),
        );

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'quiz_id'       => array( 'quiz_id', true ),
            'user_id'       => array( 'user_id', true ),
            'user_ip'       => array( 'user_ip', true ),
            'start_date'    => array( 'start_date', true ),
            'score'         => array( 'score', true ),
            'user_name'     => array( 'user_name', true ),
            'user_email'    => array( 'user_email', true ),
            'user_phone'    => array( 'user_phone', true ),
            'end_date'      => array( 'end_date', true ),
            'id'            => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_hidden_columns() {
        $sortable_columns = array(
            'user_phone',
            'end_date',
            'id'
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete'
        );

        return $actions;
    }

    
    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'quiz_results_per_page', 50 );

        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_reports( $per_page, $current_page );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-result' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_reports( absint( $_GET['result'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_reports( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }

    public function results_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Quiz created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz saved.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
