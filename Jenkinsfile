pipeline {
    agent any

    environment {
        SONARQUBE_SCANNER_HOME = tool name: 'SonarQube Scanner'
        DEPENDENCY_CHECK_HOME = '/var/jenkins_home/tools/org.jenkinsci.plugins.DependencyCheck.tools.DependencyCheckInstallation/OWASP_Dependency-Check/dependency-check'
    }
    
    stages {
        stage('Check Docker') {
            steps {
                sh 'docker --version'
            }
        }
        
        stage('Clone Repository') {
            steps {
                dir('workspace') {
                    git branch: 'main', url: 'https://github.com/vroomtest/NGINX_Test'
                }
            }
        }

        stage('Dependency Check') {
            steps {
                withCredentials([string(credentialsId: 'NVD_API_KEY', variable: 'NVD_API_KEY')]) {
                    script {
                        sh 'mkdir -p workspace/webapp/dependency-check-report'
                        sh 'echo "Dependency Check Home: ${DEPENDENCY_CHECK_HOME}"'
                        sh 'ls -l ${DEPENDENCY_CHECK_HOME}/bin'
                        sh '''
                        ${DEPENDENCY_CHECK_HOME}/bin/dependency-check.sh --project "Web App" --scan workspace/webapp --format "ALL" --out workspace/webapp/dependency-check-report --nvdApiKey ${NVD_API_KEY} || true
                        '''
                    }
                }
            }
        }
        
        stage('Integration Testing') {
            steps {
                dir('workspace/webapp') {
                    script {
                        sh '''
                            set +e
                            docker-compose -f ../../compose up -d nginx php-fpm
                            sleep 10
                            composer install
                            vendor/bin/phpunit --log-junit integration-test-results.xml
                            docker-compose -f ../../compose down
                            set -e
                        '''
                    }
                }
            }
        }

        stage('UI Testing') {
            steps {
                dir('workspace/webapp') {
                    script {
                        sh '''
                            set +e
                            docker run -d --name webapp_nginx -p 127.0.0.1:80:80 -v $(pwd):/usr/share/nginx/html:ro nginx
                            sleep 10
                            curl -s http://127.0.0.1 | grep "<title>Login</title>" || echo "Nginx app did not start"
                            curl -s -X POST -F "password=StrongPass123" http://127.0.0.1 | grep "Welcome" || echo "Failed strong password test"
                            curl -s -X POST -F "password=password" http://127.0.0.1 | grep "Password is too common" || echo "Failed common password test"
                            docker stop webapp_nginx
                            docker rm webapp_nginx
                            set -e
                        '''
                    }
                }
            }
        }
        
        stage('Build Docker Image') {
            steps {
                dir('workspace/webapp') {
                    sh 'docker build -t webapp .'
                }
            }
        }
        
        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('SonarQube') {
                    withCredentials([string(credentialsId: 'SONARQUBE_KEY', variable: 'SONARQUBE_TOKEN')]) {
                        dir('workspace/webapp') {
                            sh '''
                            ${SONARQUBE_SCANNER_HOME}/bin/sonar-scanner \
                            -Dsonar.projectKey=webapp \
                            -Dsonar.sources=. \
                            -Dsonar.inclusions=**/*.php \
                            -Dsonar.host.url=http://sonarqube:9000 \
                            -Dsonar.login=${SONARQUBE_TOKEN}
                            '''
                        }
                    }
                }
            }
        }
    }
    
    post {
        failure {
            script {
                echo 'Build failed.'
            }
        }
        always {
            archiveArtifacts artifacts: 'workspace/webapp/dependency-check-report/*.*', allowEmptyArchive: true
            archiveArtifacts artifacts: 'workspace/webapp/integration-test-results.xml', allowEmptyArchive: true
        }
    }
}
