import clsx from "clsx";

interface ButtonProps {
  onClick?: (event: React.MouseEvent<HTMLButtonElement>) => void;
  label: string;
  color: keyof typeof COLORS;
  className?: string;
}

const COLORS = {
  darkGreen: "bg-dark-green hover:bg-dark-green-hover text-white",
  red: "bg-red hover:bg-red-hover text-white",
  grey: "bg-light-grey, hover:bg-light-grey-hover text-grey",
} as const;

function ListButton({ onClick, label, color, className }: ButtonProps) {
  return (
    <button
      onClick={onClick}
      className={clsx("px-3 py-1 rounded-full", COLORS[color], className)}
    >
      <span className="">{label}</span>
    </button>
  );
}

export default ListButton;
