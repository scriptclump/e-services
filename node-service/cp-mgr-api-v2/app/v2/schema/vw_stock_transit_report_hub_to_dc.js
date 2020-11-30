/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_stock_transit_report_hub_to_dc', {
    hub_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    wh_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_address1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hub_city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    hub_phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    hub_contact_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    hub_pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
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
    beat_area: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    weight: {
      type: "DOUBLE(22,5)",
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    shop_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cfc_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    crates_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    bags_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    rt_docket_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    crates_id: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    order_status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    container_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    container_value: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    dc_address1: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    dc_address2: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    dc_city: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    dc_phone_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    dc_contact_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    dc_pincode: {
      type: DataTypes.STRING(12),
      allowNull: true
    }
  }, {
    tableName: 'vw_stock_transit_report_hub_to_dc'
  });
};
