
function Button({onClick, children}) {
    return (
        <button 
        onClick={onClick}
        className="p-3 bg-dark-green text-white font-bold rounded-full shadow-xl hover:bg-green-hover transition">
            {children}
        </button>
    )
}

export default Button;