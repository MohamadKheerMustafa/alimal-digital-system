const { exec } = require("child_process");

exec(
    "php /var/www/alimal-digital-system queue:work",
    (error, stdout, stderr) => {
        if (error) {
            console.error(`Error: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Stderr: ${stderr}`);
            return;
        }
        console.log(`Stdout: ${stdout}`);
    }
);
