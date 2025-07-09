/* Estilos para notificaciones y elementos adicionales */

/* Sistema de notificaciones */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  max-width: 350px;
  background-color: white;
  border-radius: var(--border-radius-md);
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  overflow: hidden;
  transform: translateX(400px);
  opacity: 0;
  transition: all 0.3s ease-in-out;
}

.notification.show {
  transform: translateX(0);
  opacity: 1;
}

.notification-content {
  display: flex;
  align-items: center;
  padding: 15px;
  position: relative;
}

.notification::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
}

.notification-info::before {
  background-color: var(--primary-light);
}

.notification-success::before {
  background-color: var(--success-color);
}

.notification-warning::before {
  background-color: var(--warning-color);
}

.notification-error::before {
  background-color: var(--danger-color);
}

.notification-message {
  flex: 1;
  padding-right: 10px;
}

.notification-close {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  color: var(--text-muted);
}

.notification-close:hover {
  color: var(--danger-color);
}

/* Vista previa de archivos */
.file-preview {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.file-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 5px;
  font-size: 0.9rem;
}

.file-name {
  font-weight: 500;
  color: var(--primary-color);
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
  max-width: 200px;
}

.file-size {
  color: var(--text-muted);
  font-size: 0.85rem;
}

.file-thumbnail {
  max-width: 150px;
  max-height: 150px;
  border-radius: var(--border-radius-sm);
  border: 1px solid var(--border-color);
  object-fit: cover;
}

.file-icon {
  font-size: 3rem;
  color: var(--text-muted);
  margin: 10px 0;
}

/* Animaciones de carga */
.loading {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}

.loading div {
  position: absolute;
  top: 33px;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  background: var(--primary-light);
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}

.loading div:nth-child(1) {
  left: 8px;
  animation: loading1 0.6s infinite;
}

.loading div:nth-child(2) {
  left: 8px;
  animation: loading2 0.6s infinite;
}

.loading div:nth-child(3) {
  left: 32px;
  animation: loading2 0.6s infinite;
}

.loading div:nth-child(4) {
  left: 56px;
  animation: loading3 0.6s infinite;
}

@keyframes loading1 {
  0% { transform: scale(0); }
  100% { transform: scale(1); }
}

@keyframes loading2 {
  0% { transform: translate(0, 0); }
  100% { transform: translate(24px, 0); }
}

@keyframes loading3 {
  0% { transform: scale(1); }
  100% { transform: scale(0); }
}

/* Mejoras para formulario multi-paso */
.form-steps-container {
  margin-bottom: 30px;
}

.steps-nav {
  display: flex;
  justify-content: space-between;
  position: relative;
  margin-top: 15px;
}

.steps-nav::before {
  content: '';
  position: absolute;
  top: 15px;
  left: 0;
  right: 0;
  height: 2px;
  background-color: var(--border-color);
  z-index: 1;
}

.progress-step {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  color: var(--text-muted);
  font-size: 0.85rem;
  width: 100%;
}

.step-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: white;
  border: 2px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 8px;
  transition: all 0.3s ease;
}

.progress-step.active .step-icon {
  background-color: var(--primary-light);
  border-color: var(--primary-light);
  color: white;
}

.progress-step.completed .step-icon {
  background-color: var(--success-color);
  border-color: var(--success-color);
  color: white;
}

.progress-step.completed .step-icon::after {
  content: '✓';
}

.step-label {
  font-weight: 500;
  transition: all 0.3s ease;
}

.progress-step.active .step-label {
  color: var(--primary-light);
  font-weight: 600;
}

.progress-step.completed .step-label {
  color: var(--success-color);
}

/* Navegación de pasos */
.step-navigation {
  display: flex;
  justify-content: space-between;
  margin-top: 30px;
}

/* Clases auxiliares para animaciones */
.fade-in {
  animation: fadeIn 0.5s ease forwards;
}

.fade-out {
  animation: fadeOut 0.5s ease forwards;
}

.slide-in-right {
  animation: slideInRight 0.5s ease forwards;
}

.slide-out-left {
  animation: slideOutLeft 0.5s ease forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeOut {
  from { opacity: 1; }
  to { opacity: 0; }
}

@keyframes slideInRight {
  from { transform: translateX(50px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutLeft {
  from { transform: translateX(0); opacity: 1; }
  to { transform: translateX(-50px); opacity: 0; }
}

/* Responsive para steps */
@media (max-width: 768px) {
  .step-label {
    display: none;
  }
  
  .step-icon {
    margin-bottom: 0;
  }
  
  .steps-nav::before {
    top: 15px;
  }
}

/* Modo oscuro para elementos adicionales */
.dark .notification {
  background-color: var(--dark-color);
  color: var(--text-light);
}

.dark .file-name {
  color: var(--primary-light);
}

.dark .file-thumbnail {
  border-color: var(--border-color);
}

.dark .step-icon {
  background-color: var(--dark-color);
}