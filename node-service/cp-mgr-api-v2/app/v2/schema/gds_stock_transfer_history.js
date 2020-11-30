/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_stock_transfer_history', {
    stock_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    from_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    to_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    st_del_ex_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    st_del_mobile: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    st_del_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    st_vehicle_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    st_driver_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    st_driver_mobile: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    st_received_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    st_received_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    st_docket_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    rt_del_ex_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    rt_del_mobile: {
      type: DataTypes.STRING(60),
      allowNull: true
    },
    rt_del_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    rt_vehicle_no: {
      type: DataTypes.STRING(60),
      allowNull: true
    },
    rt_driver_name: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    rt_driver_mobile: {
      type: DataTypes.STRING(60),
      allowNull: true
    },
    rt_received_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    rt_received_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    rt_docket_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    rt_vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'gds_stock_transfer_history'
  });
};
