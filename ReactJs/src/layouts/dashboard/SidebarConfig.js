// component
import Iconify from '../../components/Iconify';

// ----------------------------------------------------------------------

const getIcon = (name) => <Iconify icon={name} width={22} height={22} />;

const sidebarConfig = [
  {
    title: 'user',
    path: '/dashboard/user',
    icon: getIcon('eva:people-fill')
  },
];

export default sidebarConfig;
