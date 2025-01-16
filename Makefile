# 🐳 Docker commands for managing the fruits-and-vegetables application

# 📥 Pull the Docker image
pull:
	@echo "🔄 Pulling the Docker image..."
	docker pull tturkowski/fruits-and-vegetables

# 🧱 Build the Docker image
build:
	@echo "🏗️ Building the Docker image..."
	docker build -t tturkowski/fruits-and-vegetables -f docker/Dockerfile .

# 🏃‍♂️ Run the container interactively
run:
	@echo "🚀 Running the container interactively..."
	docker run -it -w/app -v $(shell pwd):/app tturkowski/fruits-and-vegetables sh

# 🛂 Run the tests inside the container
test:
	@echo "🧪 Running tests..."
	docker run -it -w /app -v $(shell pwd):/app tturkowski/fruits-and-vegetables bin/phpunit

# ⌨️ Start the development server
dev:
	@echo "🌐 Starting the development server..."
	docker run -it -w /app -v $(shell pwd):/app -p 8080:8080 tturkowski/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public
	@echo "➡️ Open http://127.0.0.1:8080 in your browser"

# 🔧 Clean up Docker containers, images, and volumes
clean:
	@echo "🧹 Cleaning up Docker resources..."
	docker compose down --volumes --remove-orphans
	docker system prune -f --volumes

# 🛠️ Stop all running Docker containers
stop:
	@echo "🛑 Stopping all running Docker containers..."
	docker stop $$(docker ps -q)

# 📜 Show available Makefile commands
help:
	@echo "ℹ️ Available commands:"
	@echo "   pull    - Pull the Docker image"
	@echo "   build   - Build the Docker image"
	@echo "   run     - Run the container interactively"
	@echo "   test    - Run tests inside the container"
	@echo "   dev     - Start the development server"
	@echo "   clean   - Clean up Docker resources"
	@echo "   stop    - Stop all running Docker containers"
	@echo "   help    - Show this help message"
