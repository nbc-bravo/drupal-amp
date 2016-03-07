<?php

/**
 * @file
 * Template for amp-analytics.
 *
 * Available variables:
 * - account: The analytics account ID.
 * - attributes: The HTML attributes for amp-analytics, primarily:
 *   - type: The type of analytics account.
 *
 * @see template_preprocess_amp_analytics()
 */
?>
<amp-analytics <?php print $attributes; ?>>
<script type="application/json">
  {
    "vars": {
      "account": "<?php print $account; ?>"
    },
    "triggers": {
      "trackAmpview": {
        "on": "visible",
        "request": "pageview"
      }
    }
  }
</script>
</amp-analytics>
