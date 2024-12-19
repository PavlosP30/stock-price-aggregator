# Use Node.js as the base image
FROM node:16

# Set the working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the React app files
COPY . .

# Expose port 3000 for the development server
EXPOSE 3000

# Command to start the React app
CMD ["npm", "start"]
