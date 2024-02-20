<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DefectDojo Tests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>DefectDojo Tests</h2>
    
    <form method="post" action="index.php">
        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" class="form-control" id="start_date" name="start_date">
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" class="form-control" id="end_date" name="end_date">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    
    <br>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Fetch data from DefectDojo API
        $base_url = 'https://demo.defectdojo.org';
        $api_url = $base_url . '/api/v2/tests/?limit=800000';
        $headers = array(
            'content-type' => 'application/json',
            'Authorization' => 'Token 4c9d47de2eca88aecf5578b604f4a9319c3b5460'
        );

        // Fetch data
        $data = fetchDataFromAPI($api_url, $headers);
        $tests = json_decode($data, true);
        
        // Define an array to hold branch type counts
        $branch_type_counts = array();

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Filter tests based on date range
        $filtered_tests = array_filter($tests['results'], function ($test) use ($start_date, $end_date) {
            $updated_date = date('Y-m-d', strtotime($test['updated']));
            return $updated_date >= $start_date && $updated_date <= $end_date;
        });

        if (!empty($filtered_tests)) {
            echo '<table class="table table-bordered">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Title</th>';
            echo '<th>Lead</th>';
            echo '<th>Branch Tag</th>';
            echo '<th>Commit Hash</th>';
            echo '<th>Engagement</th>';
            echo '<th>Updated</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($filtered_tests as $test) {
                echo "<tr>";
                // Hyperlink in ID column
                echo "<td><a href='$base_url/test/{$test['id']}' target='_blank'>{$test['id']}</a></td>";
                echo "<td>{$test['title']}</td>";

                // Fetch corresponding username value of lead using /api/v2/users/ API
                $lead_id = $test['lead'];
                $user_api_url = $base_url . "/api/v2/users/?id=$lead_id";
                $user_data = fetchDataFromAPI($user_api_url, $headers);
                $lead_username = json_decode($user_data, true)['results'][0]['username'];

                echo "<td>$lead_username</td>";

                echo "<td>{$test['branch_tag']}</td>";
                echo "<td>{$test['commit_hash']}</td>";

                // Fetch corresponding engagement name using /api/v2/engagements/ API
                $engagement_id = $test['engagement'];
                $engagement_api_url = $base_url . "/api/v2/engagements/{$engagement_id}";
                $engagement_data = fetchDataFromAPI($engagement_api_url, $headers);
                $engagement_name = json_decode($engagement_data, true)['name'];

                echo "<td>$engagement_name</td>";

                echo "<td>" . date('Y-m-d', strtotime($test['updated'])) . "</td>";
                echo "</tr>";

                // Increment branch type count
                $branch_type = $test['branch_tag'];
                if (isset($branch_type_counts[$branch_type])) {
                    $branch_type_counts[$branch_type]++;
                } else {
                    $branch_type_counts[$branch_type] = 1;
                }
            }

            echo '</tbody>';
            echo '<tfoot>';
            echo '<tr>';
            echo '<td colspan="7">';
            echo '<b>Branch Type Counts:</b> ';

            // Display branch type counts
            foreach ($branch_type_counts as $branch_type => $count) {
                echo "$branch_type: $count, ";
            }
            echo "<br>";

            // Display total count
            echo "<b>Total:</b> " . array_sum($branch_type_counts);

            echo '</td>';
            echo '</tr>';
            echo '</tfoot>';
            echo '</table>';
        } else {
            echo '<p>No tests found within the specified date range.</p>';
        }
    }
    ?>
</div>

</body>
</html>
