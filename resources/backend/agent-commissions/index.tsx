import { createRoot } from 'react-dom/client';
import '../css/app.css';
import '../react/i18next';
import AgentCommissions from './AgentCommissions';
import { AgentBalance } from './CommissionApi';

const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}

const appElement = document.getElementById('agent-commissions-app');
const initialBalance: AgentBalance = appElement?.dataset.balance
    ? JSON.parse(appElement.dataset.balance)
    : null;

const root = createRoot(appElement!);
root.render(<AgentCommissions initialBalance={initialBalance} />);
