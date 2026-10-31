import { Link } from 'react-router-dom';
import concerns from '../../../storefront/concerns.json';
import './concerns.css';

export default function BotanicalConcerns() {
    return (
        <nav className="bot-concerns" aria-label="Shop by concern">
            <ul className="bot-container bot-concerns-list">
                {concerns.map((concern) => (
                    <li key={concern.slug}>
                        <Link to={`/concerns/${concern.slug}`}>
                            <img src={concern.image} alt="" width="66" height="66" />
                            <span>{concern.label}</span>
                        </Link>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
