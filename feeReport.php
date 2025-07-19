<?php
$direacory= dirname(__DIR__).DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'settings.php';
// echo $direacory;
include_once($direacory);

function formatIndianCurrency($number) {
    $decimal = '';
    if (strpos($number, '.') !== false) {
        list($number, $decimal) = explode('.', $number);
        $decimal = '.' . $decimal;
    }

    $len = strlen($number);
    if ($len > 3) {
        $last3 = substr($number, -3);
        $restUnits = substr($number, 0, $len - 3);
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        return $restUnits . "," . $last3 . $decimal;
    } else {
        return $number . $decimal;
    }
}

class FeeReport {
    private $db;

    public function __construct() {
        global $db;	
        $this->db = $db;  
    }

    public function getGroupSummaries($session) {
        $session = mysqli_real_escape_string($this->db, $session);

        $query = "
             SELECT 
            cd.sort_no,
            SUM(f.tot_amount) AS total_fee,
            SUM(f.amount_paid) AS paid,
            SUM(f.tot_amount - f.amount_paid) AS due
        FROM fee_invoice f
        JOIN class_detail cd ON f.class_id = cd.sno
        WHERE f.fee_session = '$session'
        GROUP BY cd.sort_no
        ";

        
    $result = mysqli_query($this->db, $query);
    $summary = [];

    while ($r = mysqli_fetch_assoc($result)) {
        $sortNo = strtoupper($r['sort_no']);
        $group = preg_replace('/\d+$/', '', $sortNo);

        if (!isset($summary[$group])) {
            $summary[$group] = ['total_fee' => 0, 'paid' => 0, 'due' => 0];
        }

        $summary[$group]['total_fee'] += $r['total_fee'];
        $summary[$group]['paid'] += $r['paid'];
        $summary[$group]['due'] += $r['due'];
    }

    return $summary;
    }

    public function getSummaryByClass($session) {
        $session = mysqli_real_escape_string($this->db, $session);

        $query = "
            SELECT 
                cd.sno AS class_id,
                cd.class_description,
                cd.sort_no,
                COUNT(DISTINCT f.student_id) AS student_count,
                SUM(f.tot_amount) AS total_fee,
                SUM(f.amount_paid) AS paid,
                SUM(f.tot_amount - f.amount_paid) AS due
            FROM fee_invoice f
            JOIN class_detail cd ON f.class_id = cd.sno
            WHERE f.fee_session = '$session'
            GROUP BY cd.sno, cd.class_description, cd.sort_no
            ORDER BY cd.sort_no, cd.class_description
        ";

        $result = mysqli_query($this->db, $query);
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getGroupCodes() {
        $query = "SELECT DISTINCT sort_no FROM class_detail WHERE sort_no IS NOT NULL";
        $result = mysqli_query($this->db, $query);

        $allSortNos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $allSortNos[] = $row['sort_no'];
        }

        $uniqueGroups = [];
        foreach ($allSortNos as $code) {
            $base = explode('_', $code)[0];
            $uniqueGroups[$base] = true;
        }

        return array_keys($uniqueGroups);
    }

    public function getClassesByGroup($group, $session) {
        $group = mysqli_real_escape_string($this->db, $group);
        $session = mysqli_real_escape_string($this->db, $session);

        $query = "
            SELECT 
                cd.sno AS class_id,
                cd.class_description,
                cd.sort_no,
                COUNT(DISTINCT f.student_id) AS student_count,
                SUM(f.tot_amount) AS total_fee,
                SUM(f.amount_paid) AS paid,
                SUM(f.tot_amount - f.amount_paid) AS due
            FROM fee_invoice f
            JOIN class_detail cd ON f.class_id = cd.sno
            WHERE cd.sort_no LIKE '$group%' AND f.fee_session = '$session'
            GROUP BY cd.sno, cd.class_description, cd.sort_no
            ORDER BY cd.class_description
        ";

        $result = mysqli_query($this->db, $query);
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getOverallSummary($session) {
        $session = mysqli_real_escape_string($this->db, $session);

        $query = "
            SELECT 
                SUM(f.tot_amount) AS total_fee,
                SUM(f.amount_paid) AS paid,
                SUM(f.tot_amount - f.amount_paid) AS due
            FROM fee_invoice f
            WHERE f.fee_session = '$session'
        ";

        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }

    public function getStudentsInClass($class_id, $session) {
        $class_id = (int)$class_id;
        $session = mysqli_real_escape_string($this->db, $session);

          $query = "
     SELECT 
            s.stu_name,
            s.father_name,
            s.roll_no,
            s.university_uin,
            f.student_id,

            -- Type-wise amount_paid
            SUM(CASE WHEN f.type = 'self' THEN f.amount_paid ELSE 0 END) AS self_fee,
            SUM(CASE WHEN f.type = 'fees' THEN f.amount_paid ELSE 0 END) AS fees_fee,
            SUM(CASE WHEN f.type = 'computer' THEN f.amount_paid ELSE 0 END) AS computer_fee,

            -- Totals
            SUM(f.tot_amount) AS total_fee,
            SUM(f.amount_paid) AS paid,
            SUM(f.tot_amount - f.amount_paid) AS due

        FROM fee_invoice f
        JOIN student_info s ON f.student_id = s.sno

        WHERE f.class_id = $class_id 
          AND f.fee_session = '$session'

        GROUP BY f.student_id, s.stu_name, s.father_name, s.roll_no, s.university_uin
        ORDER BY s.stu_name;
";

        $result = mysqli_query($this->db, $query);
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

    public function getClassName($class_id) {
        $class_id = (int)$class_id;

        $query = "SELECT class_description FROM class_detail WHERE sno = $class_id";
        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);

        return $row ? $row['class_description'] : 'Unknown Class';
    }
}
