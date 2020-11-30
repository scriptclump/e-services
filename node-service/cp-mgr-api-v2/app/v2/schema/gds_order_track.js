/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('gds_order_track', {
    order_track_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'gds_orders',
        key: 'gds_order_id'
      }
    },
    gds_order_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    invoice_order_no: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    invoice_order_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    pick_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    pick_type: {
      type: DataTypes.BIGINT,
      allowNull: true,
      defaultValue: '0'
    },
    vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    picker_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    checker_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    checked_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    scheduled_piceker_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    assign_checker_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    picked_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    delivered_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    delivery_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    picking_start_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    picking_end_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    delivery_start_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    delivery_end_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    picked_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    beat: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    area: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    invoice_amount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    dock_area: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    vechicle_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    collected_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    collected_at: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    collected_amount: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    amount_submitted_on: {
      type: DataTypes.DATE,
      allowNull: true
    },
    amount_received_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    authorized_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    authorized_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    cfc_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    bags_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    crates_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    locality: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    landmark: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    hold_count: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
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
    st_docket_no: {
      type: DataTypes.STRING(15),
      allowNull: true
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
      type: DataTypes.STRING(45),
      allowNull: true
    },
    rt_vehicle_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    in_progress: {
      type: DataTypes.INTEGER(4),
      allowNull: true
    }
  }, {
    tableName: 'gds_order_track'
  });
};
