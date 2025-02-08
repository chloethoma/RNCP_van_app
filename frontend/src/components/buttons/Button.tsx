interface ButtonProps {
    onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
    children: React.ReactNode;
    color?: keyof typeof COLORS;
    size?: keyof typeof SIZES;
}

const COLORS = {
    "dark-green": "bg-dark-green hover:bg-dark-green-hover",
    "red": "bg-red hover:bg-red-hover",
  } as const;

  const SIZES = {
    "default": "p-3",
    "small": "p-1"
  } as const;
  
  function Button({ onClick, children, color = "dark-green", size = "default" }: ButtonProps) {
    return (
      <button 
        onClick={onClick}
        className={`text-white font-bold rounded-full shadow-xl transition ${COLORS[color]} ${SIZES[size]}`}
      >
        {children}
      </button>
    );
  }
  
  export default Button;
  