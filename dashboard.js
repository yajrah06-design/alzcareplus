// Professional Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    initializeCharts();
    setupEventListeners();
    startRealTimeUpdates();
    animateMetrics();
}

function initializeCharts() {
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        const performanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [
                    {
                        label: 'Response Time (s)',
                        data: [2.1, 2.3, 2.0, 2.5, 2.2, 2.4, 2.3],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Patient Satisfaction',
                        data: [4.7, 4.8, 4.9, 4.6, 4.8, 4.7, 4.8],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Days'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Response Time (s)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Satisfaction Score'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        min: 4.0,
                        max: 5.0
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    }

    // Patient Distribution Chart
    const distributionCtx = document.getElementById('patientDistributionChart');
    if (distributionCtx) {
        const distributionChart = new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Elevated', 'Critical', 'Discharged'],
                datasets: [{
                    data: [85, 12, 8, 15],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#dc2626',
                        '#6b7280'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
}

function setupEventListeners() {
    // Time range selector
    const timeRangeSelect = document.querySelector('.time-range-select');
    if (timeRangeSelect) {
        timeRangeSelect.addEventListener('change', function() {
            updatePerformanceChart(this.value);
        });
    }

    // Quick action items
    const quickActionItems = document.querySelectorAll('.quick-action-item');
    quickActionItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Activity refresh button
    const refreshBtn = document.querySelector('[onclick="refreshActivity()"]');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            refreshActivity();
        });
    }
}

function updatePerformanceChart(days) {
    // Simulate data update based on selected time range
    const performanceChart = Chart.getChart('performanceChart');
    if (performanceChart) {
        // Generate new data based on days
        const newData = generatePerformanceData(days);
        performanceChart.data.datasets[0].data = newData.responseTime;
        performanceChart.data.datasets[1].data = newData.satisfaction;
        performanceChart.data.labels = newData.labels;
        performanceChart.update('active');
    }
}

function generatePerformanceData(days) {
    const labels = [];
    const responseTime = [];
    const satisfaction = [];
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('en-US', { weekday: 'short' }));
        
        // Generate realistic data
        responseTime.push(+(2 + Math.random() * 0.8).toFixed(1));
        satisfaction.push(+(4.5 + Math.random() * 0.5).toFixed(1));
    }
    
    return { labels, responseTime, satisfaction };
}

function refreshActivity() {
    const activityFeed = document.querySelector('.activity-feed');
    if (activityFeed) {
        // Add loading state
        activityFeed.style.opacity = '0.5';
        
        // Simulate API call
        setTimeout(() => {
            // Add new activity item
            const newActivity = createActivityItem({
                time: 'Just now',
                type: 'health',
                patient: 'New Patient',
                action: 'System refresh completed',
                status: 'completed'
            });
            
            activityFeed.insertBefore(newActivity, activityFeed.firstChild);
            
            // Remove oldest item if more than 6
            const items = activityFeed.querySelectorAll('.activity-item');
            if (items.length > 6) {
                items[items.length - 1].remove();
            }
            
            activityFeed.style.opacity = '1';
            
            // Show notification
            showNotification('Activity feed refreshed', 'success');
        }, 1000);
    }
}

function createActivityItem(activity) {
    const item = document.createElement('div');
    item.className = `activity-item status-${activity.status}`;
    
    const iconClass = activity.type === 'alert' ? 'exclamation-triangle' : 
                     activity.type === 'medication' ? 'pills' : 
                     activity.type === 'gps' ? 'map-marker-alt' : 
                     activity.type === 'health' ? 'heartbeat' : 'user';
    
    item.innerHTML = `
        <div class="activity-icon">
            <i class="fas fa-${iconClass}"></i>
            </div>
        <div class="activity-content">
            <div class="activity-title">${activity.patient}</div>
            <div class="activity-description">${activity.action}</div>
            </div>
        <div class="activity-time">${activity.time}</div>
    `;
    
    return item;
}

function startRealTimeUpdates() {
    // Update metrics every 30 seconds
    setInterval(() => {
        updateMetrics();
    }, 30000);
    
    // Update system health every 10 seconds
    setInterval(() => {
        updateSystemHealth();
    }, 10000);
}

function updateMetrics() {
    // Simulate real-time metric updates
    const metricValues = document.querySelectorAll('.metric-value');
    metricValues.forEach(value => {
        if (value.textContent.includes('%')) {
            const currentValue = parseFloat(value.textContent);
            const change = (Math.random() - 0.5) * 2; // -1 to +1
            const newValue = Math.max(0, Math.min(100, currentValue + change));
            value.textContent = newValue.toFixed(1) + '%';
        }
    });
}

function updateSystemHealth() {
    // Simulate system health updates
    const healthBars = document.querySelectorAll('.health-fill');
    healthBars.forEach(bar => {
        const currentWidth = parseFloat(bar.style.width);
        const change = (Math.random() - 0.5) * 10; // -5 to +5
        const newWidth = Math.max(0, Math.min(100, currentWidth + change));
        bar.style.width = newWidth + '%';
        
        // Update the corresponding value
        const healthItem = bar.closest('.health-item');
        const valueElement = healthItem.querySelector('.health-value');
        if (valueElement) {
            valueElement.textContent = Math.round(newWidth) + '%';
        }
    });
}

function animateMetrics() {
    // Animate metric cards on load
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Animate welcome section
    const welcomeSection = document.querySelector('.welcome-section');
    if (welcomeSection) {
        welcomeSection.style.opacity = '0';
        welcomeSection.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            welcomeSection.style.transition = 'all 0.8s ease';
            welcomeSection.style.opacity = '1';
            welcomeSection.style.transform = 'translateY(0)';
        }, 200);
    }
}

// Use global showNotification from header

// Export functions for global access
window.refreshActivity = refreshActivity;