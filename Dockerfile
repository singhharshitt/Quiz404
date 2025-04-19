# Use the official PHP image
FROM php:8.2-cli

# Set working directory inside the container
WORKDIR /var/www/html/

# Copy all project files into the container
COPY . /var/www/html/

# Expose the port Render will use
EXPOSE 10000

# Start PHP's built-in server
CMD ["php", "-S", "0.0.0.0:10000"]
# Install Composer and dependencies
