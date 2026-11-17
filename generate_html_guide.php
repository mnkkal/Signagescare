<?php

// Script to create a formatted Word / Google Docs ready file (.docx)
$docText = file_get_contents('C:/Users/MAYANK/.gemini/antigravity/brain/9aa7e348-1215-4356-81bd-7bba84176769/signage_care_crm_lead_lifecycle_and_roles_doc.md');

// Generate an HTML document with Word/Google Docs styling headers that can be opened or uploaded directly to Google Docs
$html = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Signage Care CRM — Complete Operational Flow & Role Specification Guide</title>
<style>
  body { font-family: "Calibri", "Arial", sans-serif; line-height: 1.6; color: #1e293b; max-width: 850px; margin: 40px auto; padding: 20px; }
  h1 { color: #0f172a; border-bottom: 2px solid #3b82f6; padding-bottom: 8px; font-size: 26px; }
  h2 { color: #1e3a8a; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-top: 30px; font-size: 20px; }
  h3 { color: #1e40af; font-size: 16px; margin-top: 20px; }
  h4 { color: #334155; font-size: 14px; margin-top: 15px; }
  table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px; }
  th, td { border: 1px solid #cbd5e1; padding: 10px 12px; text-align: left; vertical-align: top; }
  th { background-color: #f1f5f9; color: #0f172a; font-weight: bold; }
  tr:nth-child(even) { background-color: #f8fafc; }
  pre, code { font-family: "Consolas", "Courier New", monospace; background-color: #f1f5f9; border-radius: 4px; }
  pre { padding: 14px; overflow-x: auto; font-size: 12px; border: 1px solid #e2e8f0; line-height: 1.4; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
  .callout { background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; margin: 15px 0; border-radius: 0 8px 8px 0; }
  ul, ol { padding-left: 24px; }
  li { margin-bottom: 6px; }
</style>
</head>
<body>
';

// Parse markdown to HTML cleanly
$lines = explode("\n", $docText);
$inCode = false;
$inTable = false;

foreach ($lines as $line) {
    if (strpos($line, '```') === 0) {
        if ($inCode) {
            $html .= "</pre>\n";
            $inCode = false;
        } else {
            $html .= "<pre>";
            $inCode = true;
        }
        continue;
    }
    if ($inCode) {
        $html .= htmlspecialchars($line) . "\n";
        continue;
    }

    if (strpos($line, '# ') === 0) {
        $html .= '<h1>' . htmlspecialchars(substr($line, 2)) . "</h1>\n";
    } elseif (strpos($line, '## ') === 0) {
        $html .= '<h2>' . htmlspecialchars(substr($line, 3)) . "</h2>\n";
    } elseif (strpos($line, '### ') === 0) {
        $html .= '<h3>' . htmlspecialchars(substr($line, 4)) . "</h3>\n";
    } elseif (strpos($line, '#### ') === 0) {
        $html .= '<h4>' . htmlspecialchars(substr($line, 5)) . "</h4>\n";
    } elseif (strpos($line, '| ') === 0) {
        if (strpos($line, '| :---') !== false || strpos($line, '| ---') !== false) {
            continue; // separator
        }
        if (!$inTable) {
            $html .= "<table>\n";
            $inTable = true;
            $cells = array_map('trim', explode('|', trim($line, '|')));
            $html .= "<thead><tr>";
            foreach ($cells as $c) {
                $html .= '<th>' . preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', htmlspecialchars($c)) . '</th>';
            }
            $html .= "</tr></thead>\n<tbody>\n";
        } else {
            $cells = array_map('trim', explode('|', trim($line, '|')));
            $html .= "<tr>";
            foreach ($cells as $c) {
                $formatted = htmlspecialchars($c);
                $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted);
                $formatted = preg_replace('/&lt;br&gt;/', '<br>', $formatted);
                $html .= '<td>' . $formatted . '</td>';
            }
            $html .= "</tr>\n";
        }
    } else {
        if ($inTable) {
            $html .= "</tbody></table>\n";
            $inTable = false;
        }
        if (trim($line) === '') {
            $html .= "<br>\n";
        } elseif (strpos($line, '* ') === 0 || strpos($line, '- ') === 0) {
            $formatted = htmlspecialchars(substr($line, 2));
            $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted);
            $html .= '<li>' . $formatted . "</li>\n";
        } else {
            $formatted = htmlspecialchars($line);
            $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted);
            $html .= '<p>' . $formatted . "</p>\n";
        }
    }
}

if ($inTable) {
    $html .= "</tbody></table>\n";
}
if ($inCode) {
    $html .= "</pre>\n";
}

$html .= '</body></html>';

file_put_contents('d:/Work/Backdrop Advt/public/Signage_Care_CRM_Operational_Guide.html', $html);
file_put_contents('C:/Users/MAYANK/.gemini/antigravity/brain/9aa7e348-1215-4356-81bd-7bba84176769/Signage_Care_CRM_Operational_Guide.html', $html);
echo "HTML Guide Generated Successfully\n";
