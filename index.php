<?php
        // This function returns a directory listing of the EWICS folder, but really it could be use for anything

        // GET Parameters:
        // direct (bol)         Flag to toggle between file to be downloaded or just viewed
        // filter (str)         Filter to decide what to filter using grep, is case insensitive
        // table (bol)          Flag to enable parsing of the data to a HTML table, assumes direct

        // Static Variable
        $filename       = "dump.txt";           // File name of the textfile that is getting dumped out
        $folder         = "/media/ewics";       // Folder that is getting listed

        // Checks if the user wants to filter
        if ( isset($_GET["filter"]) )
        {
                // GET a variable for limiting file selection and sanitizes it a much as possible
                $grep = filter_var($_GET["filter"], FILTER_SANITIZE_STRIPPED);

                // Get Directory listing and parses for filter string. The '-i' in grep is to make
                // case insensitive.
                $file = shell_exec("du -a --time $folder | grep -i $grep");
        }

        // Otherwise don't grep anything
        else
        {
                $file = shell_exec("du -a --time $folder");
        }

        // Test if the GET variable direct is set. We're not taking any data from the variable

        // If direct is not set, aka unless it is called then, the default is to save it as a file
        // Also if table is called, then we assume it is for web
        if ( !(isset($_GET["direct"]) or isset($_GET["table"]) ) )
        {
                // Let the browser know that it's going to download stuff
                header("Content-type: text/plain");
                header("Content-Disposition: attachment; filename=$filename");
        }

        // Switch Linux CR formatting for Windows' explict CR+LF
        $file = preg_replace('~\R~u', "\r\n", $file);

        // Check if table has been called
        if ( isset($_GET["table"]) )
        {
                // Parse to a table for web viewing

                // Just in case we want it to be a stand alone page
                //echo "<html><body>";

                // Start table
                echo "<table><tr><th>Size</th><th>Last Modified</th><th>File Path</th>";

                // Explode each line into $line
                foreach (str_getcsv($file, "\n") as $line)
                {
                        // Start Row
                        echo "<tr>";

                        // Prints out each line of the CSV
                        foreach (str_getcsv($line, "\t") as $cell)
                                echo "<td>" . htmlspecialchars($cell) . "</td>";

                        //End row
                        echo "</tr>\n";
                }

                // Close the table
                echo "</table>";
        }
        else
        {
                // Write out listing, is trimmed to make sure there no extra lines that will bugger things up
                print trim($file);
        }


        // Close tags if we ever use them, don't use. It for the open tags to display the table
        // echo "</body></html>";

        // Escape, should return 0
        exit();
?>
